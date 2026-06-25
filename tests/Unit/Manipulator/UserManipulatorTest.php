<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Manipulator;

use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Event\UserEvent;
use Softspring\UserBundle\Manager\AdminUserManagerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Manipulator\UserManipulator;
use Softspring\UserBundle\SfsUserEvents;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class UserManipulatorTest extends TestCase
{
    public function testCreatesRegularUser(): void
    {
        $user = new User();
        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->once())->method('createEntity')->willReturn($user);
        $userManager->expects($this->once())->method('saveEntity')->with($user);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(static fn (UserEvent $event): bool => $event->getUser() === $user),
                SfsUserEvents::USER_CREATED
            )
            ->willReturnArgument(0);

        $requestStack = new RequestStack();
        $requestStack->push(Request::create('/create-user'));

        $created = (new UserManipulator($userManager, $this->createMock(AdminUserManagerInterface::class), $dispatcher, $requestStack))
            ->create('', 'ada@example.com', 'plain-password', ['ROLE_EDITOR'], false);

        $this->assertSame($user, $created);
        $this->assertSame('ada@example.com', $created->getEmail());
        $this->assertSame('plain-password', $created->getPlainPassword());
        $this->assertSame(['ROLE_EDITOR', 'ROLE_USER'], $created->getRoles());
        $this->assertFalse($created->isAdmin());
        $this->assertFalse($created->isSuperAdmin());
    }

    public function testCreatesAdminUserThroughAdminManager(): void
    {
        $user = new User();
        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->expects($this->never())->method('createEntity');
        $userManager->expects($this->once())->method('saveEntity')->with($user);

        $adminUserManager = $this->createMock(AdminUserManagerInterface::class);
        $adminUserManager->expects($this->once())->method('createEntity')->willReturn($user);

        $created = (new UserManipulator($userManager, $adminUserManager, $this->createMock(EventDispatcherInterface::class), new RequestStack()))
            ->create('', 'root@example.com', 'secret', ['ROLE_ADMIN'], true, true, true);

        $this->assertSame($user, $created);
        $this->assertSame('root@example.com', $created->getEmail());
        $this->assertTrue($created->isAdmin());
        $this->assertTrue($created->isSuperAdmin());
        $this->assertContains('ROLE_ADMIN', $created->getRoles());
        $this->assertContains('ROLE_SUPER_ADMIN', $created->getRoles());
    }
}
