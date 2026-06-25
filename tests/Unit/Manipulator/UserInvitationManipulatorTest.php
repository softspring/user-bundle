<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Manipulator;

use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\Manipulator\UserInvitationManipulator;
use Softspring\UserBundle\Mime\Example\Model\ExampleInvitation;
use Softspring\UserBundle\SfsUserEvents;
use Softspring\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UserInvitationManipulatorTest extends TestCase
{
    public function testCreatesAndDispatchesInvitation(): void
    {
        $invitation = new ExampleInvitation();
        $manager = $this->createMock(UserInvitationManagerInterface::class);
        $manager->expects($this->once())->method('createEntity')->willReturn($invitation);
        $manager->expects($this->once())->method('saveEntity')->with($invitation);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(static fn (UserInvitationEvent $event): bool => $event->getInvitation() === $invitation),
                SfsUserEvents::USER_INVITED
            )
            ->willReturnArgument(0);

        $requestStack = new RequestStack();
        $requestStack->push(Request::create('/invite'));

        $tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $tokenGenerator->expects($this->once())->method('generateToken')->willReturn('generated-token');

        $created = (new UserInvitationManipulator($manager, $dispatcher, $requestStack, $tokenGenerator))
            ->invite('ada@example.com', 'ada', ['ROLE_EDITOR', 'ROLE_ADMIN']);

        $this->assertSame($invitation, $created);
        $this->assertSame('ada@example.com', $created->getEmail());
        $this->assertSame('ada@example.com', $created->getUserIdentifier());
        $this->assertSame('generated-token', $created->getInvitationToken());
        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $created->getRoles());
        $this->assertTrue($created->isAdmin());
        $this->assertFalse($created->isSuperAdmin());
    }
}
