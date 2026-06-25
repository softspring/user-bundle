<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Mailer;

use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Mailer\Exception\InvalidUserClassException;
use Softspring\UserBundle\Mailer\UserMailer;
use Softspring\UserBundle\Mime\ConfirmationEmail;
use Softspring\UserBundle\Mime\InvitationEmail;
use Softspring\UserBundle\Mime\ResetPasswordEmail;
use Softspring\UserBundle\Mime\Example\Model\ExampleInvitation;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserMailerTest extends TestCase
{
    public function testSendsRegisterConfirmationEmail(): void
    {
        $user = $this->createUser();
        $user->setConfirmationToken('confirm-token');
        $user->setLocale('es');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with('sfs_user_register_confirm', [
                'user' => $user->getId(),
                'token' => 'confirm-token',
            ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://example.com/confirm');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (RawMessage $message): bool {
                $this->assertInstanceOf(ConfirmationEmail::class, $message);
                $this->assertSame('ada@example.com', $message->getTo()[0]->getAddress());
                $this->assertSame('Ada Lovelace', $message->getTo()[0]->getName());
                $this->assertSame('es', $message->getLocale());

                return true;
            }));

        (new UserMailer($mailer, $urlGenerator, $this->createTranslator()))->sendRegisterConfirmationEmail($user, 'en');
    }

    public function testSendsInvitationEmail(): void
    {
        $invitation = new ExampleInvitation();
        $invitation->setEmail('grace@example.com');
        $invitation->setName('Grace');
        $invitation->setSurname('Hopper');
        $invitation->setInvitationToken('invite-token');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with('sfs_user_invitation_accept', ['token' => 'invite-token'], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://example.com/invite');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (RawMessage $message): bool {
                $this->assertInstanceOf(InvitationEmail::class, $message);
                $this->assertSame('grace@example.com', $message->getTo()[0]->getAddress());
                $this->assertSame('Grace Hopper', $message->getTo()[0]->getName());

                return true;
            }));

        (new UserMailer($mailer, $urlGenerator, $this->createTranslator()))->sendInvitationEmail($invitation, 'en');
    }

    public function testSendsResetPasswordEmail(): void
    {
        $user = $this->createUser();
        $user->setPasswordRequestToken('reset-token');

        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with('sfs_user_reset_password', [
                'user' => $user->getId(),
                'token' => 'reset-token',
            ], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn('https://example.com/reset');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (RawMessage $message): bool {
                $this->assertInstanceOf(ResetPasswordEmail::class, $message);
                $this->assertSame('ada@example.com', $message->getTo()[0]->getAddress());

                return true;
            }));

        (new UserMailer($mailer, $urlGenerator, $this->createTranslator()))->sendResettingEmail($user);
    }

    public function testRegisterConfirmationRejectsUnsupportedUser(): void
    {
        $this->expectException(InvalidUserClassException::class);
        $this->expectExceptionMessage('must implements');

        (new UserMailer($this->createMock(MailerInterface::class), $this->createMock(UrlGeneratorInterface::class), $this->createTranslator()))
            ->sendRegisterConfirmationEmail(new MinimalMailerUser());
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setEmail('ada@example.com');
        $user->setName('Ada');
        $user->setSurname('Lovelace');

        return $user;
    }

    private function createTranslator(): TranslatorInterface
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return $translator;
    }
}

class MinimalMailerUser implements UserInterface
{
    public function getId(): mixed
    {
        return 'minimal';
    }

    public function getUserIdentifier(): string
    {
        return 'minimal';
    }

    public function getDisplayName(): string
    {
        return 'Minimal';
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }

    public function __serialize(): array
    {
        return [];
    }

    public function __unserialize(array $data): void
    {
    }
}
