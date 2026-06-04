<?php

namespace Softspring\UserBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\DependencyInjection\SfsUserExtension;
use Softspring\UserBundle\EventListener\EmailInvitationListener;
use Softspring\UserBundle\EventListener\SendResetPasswordEmailListener;
use Softspring\UserBundle\EventListener\UserRegistrationListener;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SfsUserExtensionTest extends TestCase
{
    public function testDoesNotRegisterMailerServicesByDefault(): void
    {
        $container = new ContainerBuilder();

        (new SfsUserExtension())->load([[
            'invite' => [
                'enabled' => true,
            ],
        ]], $container);

        self::assertFalse($container->hasDefinition(UserMailerInterface::class));
        self::assertFalse($container->hasDefinition(SendResetPasswordEmailListener::class));
        self::assertFalse($container->hasDefinition(UserRegistrationListener::class));
        self::assertFalse($container->hasDefinition(EmailInvitationListener::class));
    }

    public function testThrowsWhenMailerIsEnabledWithoutSymfonyMailer(): void
    {
        $container = new ContainerBuilder();
        $extension = new class extends SfsUserExtension {
            protected function hasSymfonyMailer(): bool
            {
                return false;
            }
        };

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The "sfs_user.mailer" feature requires symfony/mailer.');

        $extension->load([[
            'mailer' => [
                'enabled' => true,
            ],
        ]], $container);
    }
}
