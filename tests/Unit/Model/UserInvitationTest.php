<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Model;

use DateTime;
use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Mime\Example\Model\ExampleInvitation;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;

class UserInvitationTest extends TestCase
{
    public function testInvitationStateAccessors(): void
    {
        $invitation = new ExampleInvitation();
        $inviter = new User();
        $acceptedUser = new User();

        $invitation->setInvitationToken('token-123');
        $invitation->setAcceptedAt(new DateTime('@1700000000'));
        $invitation->setInviter($inviter);
        $invitation->setUser($acceptedUser);

        $this->assertSame('token-123', $invitation->getInvitationToken());
        $this->assertSame('1700000000', $invitation->getAcceptedAt()->format('U'));
        $this->assertSame($inviter, $invitation->getInviter());
        $this->assertSame($acceptedUser, $invitation->getUser());
        $this->assertSame((string) $invitation->getId(), (string) $invitation);

        $invitation->setAcceptedAt(null);
        $this->assertNull($invitation->getAcceptedAt());
    }
}
