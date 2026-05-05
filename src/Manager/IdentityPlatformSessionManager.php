<?php

namespace Softspring\UserBundle\Manager;

use RuntimeException;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformAuthenticationResult;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformGoogleAuthenticator;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformGoogleUser;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformTokens;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformUserSynchronizer;
use Softspring\UserBundle\GoogleIdentityPlatform\LocalUserAlreadyExistsException;
use Softspring\UserBundle\GoogleIdentityPlatform\LocalUserNotFoundException;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IdentityPlatformSessionManager implements IdentityPlatformSessionManagerInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly IdentityPlatformGoogleAuthenticator $googleAuthenticator,
        private readonly IdentityPlatformUserSynchronizer $userSynchronizer,
        private readonly UserManagerInterface $userManager,
        private readonly string $apiKey,
        private readonly ?string $tenantId,
    ) {
    }

    public function authenticateGoogleCredential(string $requestUri, string $googleCredential): IdentityPlatformAuthenticationResult
    {
        $authentication = $this->googleAuthenticator->authenticateWithTokens($requestUri, $googleCredential);

        return new IdentityPlatformAuthenticationResult(
            syncResult: $this->userSynchronizer->sync($authentication->user),
            tokens: $authentication->tokens,
        );
    }

    public function loginWithGoogleCredential(string $requestUri, string $googleCredential): IdentityPlatformAuthenticationResult
    {
        $authentication = $this->googleAuthenticator->authenticateWithTokens($requestUri, $googleCredential);
        $existingUser = $this->userSynchronizer->findExistingUser($authentication->user);

        if (!$existingUser instanceof UserInterface) {
            throw new LocalUserNotFoundException('No local account matches this Google account. Use registration first.');
        }

        return new IdentityPlatformAuthenticationResult(
            syncResult: $this->userSynchronizer->sync($authentication->user),
            tokens: $authentication->tokens,
        );
    }

    public function registerWithGoogleCredential(string $requestUri, string $googleCredential): IdentityPlatformAuthenticationResult
    {
        $authentication = $this->googleAuthenticator->authenticateWithTokens($requestUri, $googleCredential);
        $existingUser = $this->userSynchronizer->findExistingUser($authentication->user);

        if ($existingUser instanceof UserInterface) {
            throw new LocalUserAlreadyExistsException('A local account already exists for this Google account. Use sign in instead.');
        }

        return new IdentityPlatformAuthenticationResult(
            syncResult: $this->userSynchronizer->sync($authentication->user),
            tokens: $authentication->tokens,
        );
    }

    public function loadUserFromIdToken(string $idToken): UserInterface
    {
        $identityUser = $this->loadIdentityUser($idToken);

        $existingUser = $this->userManager->findUserBy(['identityPlatformUserId' => $identityUser->identityPlatformUserId]);

        if ($existingUser instanceof UserInterface) {
            return $existingUser;
        }

        return $this->userSynchronizer->sync($identityUser)->user;
    }

    public function refreshTokens(string $refreshToken): IdentityPlatformTokens
    {
        $this->assertApiKeyConfigured();

        if ('' === trim($refreshToken)) {
            throw new RuntimeException('Identity Platform refresh token is required.');
        }

        $payload = $this->requestJson(
            'POST',
            'https://securetoken.googleapis.com/v1/token',
            [
                'query' => [
                    'key' => $this->apiKey,
                ],
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => http_build_query([
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ]),
            ],
            'Identity Platform rejected the refresh token.'
        );

        $accessToken = is_string($payload['id_token'] ?? null) ? trim($payload['id_token']) : '';
        $nextRefreshToken = is_string($payload['refresh_token'] ?? null) ? trim($payload['refresh_token']) : '';
        $expiresIn = $this->parseExpiresIn($payload['expires_in'] ?? null);

        if ('' === $accessToken || '' === $nextRefreshToken || null === $expiresIn) {
            throw new RuntimeException('Identity Platform refresh response is missing required fields.');
        }

        return new IdentityPlatformTokens($accessToken, $nextRefreshToken, $expiresIn);
    }

    private function loadIdentityUser(string $idToken): IdentityPlatformGoogleUser
    {
        $this->assertApiKeyConfigured();

        if ('' === trim($idToken)) {
            throw new RuntimeException('Identity Platform id token is required.');
        }

        $payload = $this->requestJson(
            'POST',
            'https://identitytoolkit.googleapis.com/v1/accounts:lookup',
            [
                'query' => [
                    'key' => $this->apiKey,
                ],
                'json' => [
                    'idToken' => trim($idToken),
                ],
            ],
            'Identity Platform rejected the id token.'
        );

        $users = $payload['users'] ?? null;

        if (!is_array($users) || !is_array($users[0] ?? null)) {
            throw new RuntimeException('Identity Platform lookup response does not contain a valid user.');
        }

        return $this->mapLookupUser($users[0]);
    }

    /**
     * @param array<string, mixed> $userPayload
     */
    private function mapLookupUser(array $userPayload): IdentityPlatformGoogleUser
    {
        $identityPlatformUserId = is_string($userPayload['localId'] ?? null) ? trim($userPayload['localId']) : '';

        if ('' === $identityPlatformUserId) {
            throw new RuntimeException('Identity Platform lookup response is missing the local user id.');
        }

        $providerInfo = $this->extractProviderInfo($userPayload['providerUserInfo'] ?? null);
        $providerId = is_string($providerInfo['providerId'] ?? null) && '' !== trim($providerInfo['providerId'])
            ? trim($providerInfo['providerId'])
            : 'identity_platform';
        $providerUserId = is_string($providerInfo['federatedId'] ?? null) && '' !== trim($providerInfo['federatedId'])
            ? trim($providerInfo['federatedId'])
            : $identityPlatformUserId;

        $displayName = is_string($userPayload['displayName'] ?? null) ? $userPayload['displayName'] : null;
        $email = is_string($userPayload['email'] ?? null) ? $userPayload['email'] : null;

        return new IdentityPlatformGoogleUser(
            identityPlatformUserId: $identityPlatformUserId,
            identityProvider: $providerId,
            identityProviderUserId: $providerUserId,
            email: $email,
            emailVerified: (bool) ($userPayload['emailVerified'] ?? false),
            displayName: $displayName,
            firstName: $this->extractFirstName($displayName),
            lastName: $this->extractLastName($displayName),
            photoUrl: is_string($userPayload['photoUrl'] ?? null) ? $userPayload['photoUrl'] : null,
            isNewIdentityPlatformUser: false,
            tenantId: is_string($userPayload['tenantId'] ?? null) ? $userPayload['tenantId'] : $this->tenantId,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function extractProviderInfo(mixed $providerInfoList): array
    {
        if (!is_array($providerInfoList)) {
            return [];
        }

        foreach ($providerInfoList as $providerInfo) {
            if (is_array($providerInfo) && 'google.com' === ($providerInfo['providerId'] ?? null)) {
                return $providerInfo;
            }
        }

        foreach ($providerInfoList as $providerInfo) {
            if (is_array($providerInfo)) {
                return $providerInfo;
            }
        }

        return [];
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function requestJson(string $method, string $url, array $options, string $clientErrorMessage): array
    {
        try {
            $response = $this->httpClient->request($method, $url, $options);
            $payload = $response->toArray(false);
        } catch (ExceptionInterface $exception) {
            throw new RuntimeException($clientErrorMessage, (int) $exception->getCode(), $exception);
        }

        if (is_array($payload['error'] ?? null)) {
            $errorMessage = $payload['error']['message'] ?? 'Identity Platform returned an error.';
            $errorPayload = json_encode($payload['error']);

            if (is_string($errorMessage) && '' !== $errorMessage) {
                if (false !== $errorPayload) {
                    throw new RuntimeException(sprintf('Identity Platform error: %s. Payload: %s', $errorMessage, $errorPayload));
                }

                throw new RuntimeException(sprintf('Identity Platform error: %s', $errorMessage));
            }
        }

        return $payload;
    }

    private function assertApiKeyConfigured(): void
    {
        if ('' === $this->apiKey) {
            throw new RuntimeException('Identity Platform API key is not configured.');
        }
    }

    private function parseExpiresIn(mixed $value): ?int
    {
        if (is_int($value) && $value > 0) {
            return $value;
        }

        if (is_string($value) && ctype_digit($value) && (int) $value > 0) {
            return (int) $value;
        }

        return null;
    }

    private function extractFirstName(?string $displayName): ?string
    {
        if (!$displayName) {
            return null;
        }

        $parts = preg_split('/\s+/', trim($displayName)) ?: [];

        return $parts[0] ?? null;
    }

    private function extractLastName(?string $displayName): ?string
    {
        if (!$displayName) {
            return null;
        }

        $parts = preg_split('/\s+/', trim($displayName)) ?: [];

        if (count($parts) < 2) {
            return null;
        }

        array_shift($parts);

        return implode(' ', $parts);
    }
}
