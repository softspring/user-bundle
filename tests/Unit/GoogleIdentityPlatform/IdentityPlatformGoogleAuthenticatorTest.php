<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\GoogleIdentityPlatform;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformGoogleAuthenticator;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class IdentityPlatformGoogleAuthenticatorTest extends TestCase
{
    public function testAuthenticateMapsIdentityPlatformResponse(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(json_encode([
                'localId' => 'gcip-user-1',
                'providerId' => 'google.com',
                'federatedId' => 'google-user-1',
                'email' => 'user@example.com',
                'emailVerified' => true,
                'displayName' => 'Jane Doe',
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'photoUrl' => 'https://example.com/avatar.jpg',
                'isNewUser' => true,
                'tenantId' => 'tenant-a',
            ], JSON_THROW_ON_ERROR)),
        ]);

        $authenticator = new IdentityPlatformGoogleAuthenticator($httpClient, 'api-key', 'tenant-a');
        $user = $authenticator->authenticate('https://presenciaonline.softspring.dev/auth/google/one-tap', 'google-credential');

        self::assertSame('gcip-user-1', $user->identityPlatformUserId);
        self::assertSame('google.com', $user->identityProvider);
        self::assertSame('google-user-1', $user->identityProviderUserId);
        self::assertSame('user@example.com', $user->email);
        self::assertTrue($user->emailVerified);
        self::assertSame('Jane', $user->firstName);
        self::assertSame('Doe', $user->lastName);
        self::assertTrue($user->isNewIdentityPlatformUser);
    }

    public function testAuthenticateSurfacesIdentityPlatformErrorMessage(): void
    {
        $httpClient = new MockHttpClient([
            new MockResponse(json_encode([
                'error' => [
                    'message' => 'REDIRECT_URI_MISMATCH',
                ],
            ], JSON_THROW_ON_ERROR), ['http_code' => 400]),
        ]);

        $authenticator = new IdentityPlatformGoogleAuthenticator($httpClient, 'api-key', null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Identity Platform error: REDIRECT_URI_MISMATCH');

        $authenticator->authenticate('https://presenciaonline.softspring.dev/auth/google/callback', 'google-credential');
    }
}
