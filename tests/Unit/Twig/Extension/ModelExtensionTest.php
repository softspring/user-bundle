<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Twig\Extension;

use Twig\TwigFunction;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\Manager\UserInvitationManagerInterface;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Mime\Example\Model\ExampleInvitation;
use Softspring\UserBundle\Model\UserAccess;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;
use Softspring\UserBundle\Twig\Extension\ModelExtension;

class ModelExtensionTest extends TestCase
{
    public function testFunctionsExposeModelChecks(): void
    {
        $extension = new ModelExtension(null, null, null);

        $names = array_map(static fn (TwigFunction $function): string => $function->getName(), $extension->getFunctions());

        $this->assertSame(['sfs_user_is', 'sfs_user_access_is', 'sfs_invitation_is'], $names);
    }

    public function testModelChecksReturnFalseWhenManagersAreMissing(): void
    {
        $extension = new ModelExtension(null, null, null);

        $this->assertFalse($extension->userCheckInterface('NameSurname'));
        $this->assertFalse($extension->userAccessCheckInterface('LatLong'));
        $this->assertFalse($extension->userInvitationInterface('Email'));
    }

    public function testModelChecksUseConfiguredManagerEntityClasses(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->method('getEntityClassReflection')->willReturn(new ReflectionClass(User::class));

        $accessManager = $this->createMock(UserAccessManagerInterface::class);
        $accessManager->method('getEntityClassReflection')->willReturn(new ReflectionClass(UserAccess::class));

        $invitationManager = $this->createMock(UserInvitationManagerInterface::class);
        $invitationManager->method('getEntityClassReflection')->willReturn(new ReflectionClass(ExampleInvitation::class));

        $extension = new ModelExtension($userManager, $accessManager, $invitationManager);

        $this->assertTrue($extension->userCheckInterface('NameSurname'));
        $this->assertTrue($extension->userCheckInterface('UserIdentifierEmail'));
        $this->assertTrue($extension->userCheckInterface('Softspring\UserBundle\Model\ConfirmableInterface'));
        $this->assertFalse($extension->userCheckInterface('MissingContract'));

        $this->assertTrue($extension->userAccessCheckInterface('UserAccessInterface'));
        $this->assertFalse($extension->userAccessCheckInterface('LatLong'));

        $this->assertTrue($extension->userInvitationInterface('UserInvitationInterface'));
        $this->assertFalse($extension->userInvitationInterface('MissingContract'));
    }
}
