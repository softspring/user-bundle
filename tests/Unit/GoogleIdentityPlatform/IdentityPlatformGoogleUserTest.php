<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\GoogleIdentityPlatform;

use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformGoogleUser;

final class IdentityPlatformGoogleUserTest extends TestCase
{
    public function testConstructorStoresAllFields(): void
    {
        $user = new IdentityPlatformGoogleUser(
            identityPlatformUserId: 'gcip-user-1',
            identityProvider: 'google.com',
            identityProviderUserId: 'google-user-1',
            email: 'user@example.com',
            emailVerified: true,
            displayName: 'Jane Doe',
            firstName: 'Jane',
            lastName: 'Doe',
            photoUrl: 'https://example.com/avatar.jpg',
            isNewIdentityPlatformUser: true,
            tenantId: 'tenant-a',
        );

        self::assertSame('gcip-user-1', $user->identityPlatformUserId);
        self::assertSame('google.com', $user->identityProvider);
        self::assertSame('google-user-1', $user->identityProviderUserId);
        self::assertSame('user@example.com', $user->email);
        self::assertTrue($user->emailVerified);
        self::assertSame('Jane Doe', $user->displayName);
        self::assertSame('Jane', $user->firstName);
        self::assertSame('Doe', $user->lastName);
        self::assertSame('https://example.com/avatar.jpg', $user->photoUrl);
        self::assertTrue($user->isNewIdentityPlatformUser);
        self::assertSame('tenant-a', $user->tenantId);
    }
}
