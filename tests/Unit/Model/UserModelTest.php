<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Model;

use DateTime;
use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;

class UserModelTest extends TestCase
{
    public function testConfirmableStateCanBeStoredAndCleared(): void
    {
        $user = new User();

        $user->setConfirmationToken('confirm-token');
        $user->setConfirmedAt(new DateTime('@1700000000'));

        $this->assertSame('confirm-token', $user->getConfirmationToken());
        $this->assertSame('1700000000', $user->getConfirmedAt()->format('U'));
        $this->assertTrue($user->isConfirmed());

        $user->setConfirmedAt(null);

        $this->assertNull($user->getConfirmedAt());
        $this->assertFalse($user->isConfirmed());
    }

    public function testPasswordRequestStateCanBeStoredAndCleared(): void
    {
        $user = new User();

        $user->setPasswordRequestToken('reset-token');
        $user->setPasswordRequestedAt(new DateTime('@1700000000'));

        $this->assertSame('reset-token', $user->getPasswordRequestToken());
        $this->assertSame('1700000000', $user->getPasswordRequestedAt()->format('U'));

        $user->setPasswordRequestedAt(null);

        $this->assertNull($user->getPasswordRequestedAt());
    }

    public function testUserSerializationKeepsAuthenticationFields(): void
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword('encoded-password');
        $user->setSalt('salt');

        $serialized = $user->__serialize();
        $restored = new User();
        $restored->__unserialize($serialized);

        $this->assertSame('encoded-password', $restored->getPassword());
        $this->assertSame('user@example.com', $restored->getEmail());
        $this->assertNotSame('', (string) $restored);
    }
}
