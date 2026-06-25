<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Mailer\Loader;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Softspring\UserBundle\Mailer\Loader\MailTemplateLoader;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Mime\ConfirmationEmail;
use Softspring\UserBundle\Mime\InvitationEmail;
use Softspring\UserBundle\Mime\ResetPasswordEmail;
use Softspring\UserBundle\Mime\Example\Model\ExampleUser;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;

class MailTemplateLoaderTest extends TestCase
{
    public function testLoadsResetConfirmAndInvitationTemplates(): void
    {
        $loader = new MailTemplateLoader(InvitationEmail::class, $this->createUserManager(User::class));

        $templates = $loader->load()->getTemplates();

        $this->assertSame(ResetPasswordEmail::class, $templates['sfs_user.reset_password']->getClass());
        $this->assertTrue($templates['sfs_user.reset_password']->isPreview());
        $this->assertSame(ConfirmationEmail::class, $templates['sfs_user.register_confirm']->getClass());
        $this->assertSame(InvitationEmail::class, $templates['sfs_user.invite']->getClass());
    }

    public function testSkipsOptionalTemplatesWhenUnavailable(): void
    {
        $loader = new MailTemplateLoader(null, $this->createUserManager(ExampleUser::class));

        $templates = $loader->load()->getTemplates();

        $this->assertArrayHasKey('sfs_user.reset_password', $templates);
        $this->assertArrayNotHasKey('sfs_user.register_confirm', $templates);
        $this->assertArrayNotHasKey('sfs_user.invite', $templates);
    }

    private function createUserManager(string $userClass): UserManagerInterface
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager->method('getEntityClassReflection')->willReturn(new ReflectionClass($userClass));

        return $userManager;
    }
}
