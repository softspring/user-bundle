<?php

namespace Softspring\UserBundle\GoogleIdentityPlatform;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/* added-by-ea-tests final */ class IdentityPlatformGoogleAuthenticator
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $apiKey,
        private readonly ?string $tenantId,
    ) {
    }

    public function authenticate(string $requestUri, string $googleCredential): IdentityPlatformGoogleUser
    {
        return $this->authenticateWithTokens($requestUri, $googleCredential)->user;
    }

    public function authenticateWithTokens(string $requestUri, string $googleCredential): IdentityPlatformGoogleAuthenticationResult
    {
        if ('' === $this->apiKey) {
            throw new RuntimeException('Identity Platform API key is not configured.');
        }

        try {
            $response = $this->httpClient->request('POST', 'https://identitytoolkit.googleapis.com/v1/accounts:signInWithIdp', [
                'query' => [
                    'key' => $this->apiKey,
                ],
                'json' => array_filter([
                    'requestUri' => $requestUri,
                    'postBody' => http_build_query([
                        'id_token' => $googleCredential,
                        'providerId' => 'google.com',
                    ]),
                    'returnSecureToken' => true,
                    'returnIdpCredential' => true,
                    'tenantId' => $this->tenantId ?: null,
                ], static fn (string|true|null $value): bool => null !== $value),
            ]);
            $payload = $response->toArray(false);
        } catch (ExceptionInterface $exception) {
            throw new RuntimeException('Identity Platform rejected the Google credential.', $exception->getCode(), previous: $exception);
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

        if (!is_string($payload['localId'] ?? null) || !is_string($payload['providerId'] ?? null) || !is_string($payload['federatedId'] ?? null)) {
            $payloadKeys = implode(', ', array_keys($payload));

            throw new RuntimeException(sprintf('Identity Platform response is missing required fields. Keys: %s', $payloadKeys));
        }

        $accessToken = is_string($payload['idToken'] ?? null) ? trim($payload['idToken']) : '';
        $refreshToken = is_string($payload['refreshToken'] ?? null) ? trim($payload['refreshToken']) : '';
        $expiresIn = $this->parseExpiresIn($payload['expiresIn'] ?? null);

        if ('' === $accessToken || '' === $refreshToken || null === $expiresIn) {
            throw new RuntimeException('Identity Platform response is missing token fields.');
        }

        return new IdentityPlatformGoogleAuthenticationResult(
            user: new IdentityPlatformGoogleUser(
                identityPlatformUserId: $payload['localId'],
                identityProvider: $payload['providerId'],
                identityProviderUserId: $payload['federatedId'],
                email: is_string($payload['email'] ?? null) ? $payload['email'] : null,
                emailVerified: (bool) ($payload['emailVerified'] ?? false),
                displayName: is_string($payload['displayName'] ?? null) ? $payload['displayName'] : null,
                firstName: is_string($payload['firstName'] ?? null) ? $payload['firstName'] : null,
                lastName: is_string($payload['lastName'] ?? null) ? $payload['lastName'] : null,
                photoUrl: is_string($payload['photoUrl'] ?? null) ? $payload['photoUrl'] : null,
                isNewIdentityPlatformUser: (bool) ($payload['isNewUser'] ?? false),
                tenantId: is_string($payload['tenantId'] ?? null) ? $payload['tenantId'] : null,
            ),
            tokens: new IdentityPlatformTokens(
                accessToken: $accessToken,
                refreshToken: $refreshToken,
                expiresIn: $expiresIn,
            ),
        );
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
}
