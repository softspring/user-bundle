<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\GoogleIdentityPlatform;

use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\GoogleIdentityPlatform\IdentityPlatformUserSyncResult;
use Softspring\UserBundle\Model\UserInterface;

final class IdentityPlatformUserSyncResultTest extends TestCase
{
    public function testConstructorStoresAllFields(): void
    {
        $user = $this->createMock(UserInterface::class);
        $result = new IdentityPlatformUserSyncResult(
            user: $user,
            localUserCreated: true,
            newIdentityPlatformUser: false,
        );

        self::assertSame($user, $result->user);
        self::assertTrue($result->localUserCreated);
        self::assertFalse($result->newIdentityPlatformUser);
    }
}
