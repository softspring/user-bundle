<?php

declare(strict_types=1);

namespace Softspring\UserBundle\Tests\Unit\Mime;

use PHPUnit\Framework\TestCase;
use Softspring\UserBundle\Mime\ConfirmationEmail;
use Softspring\UserBundle\Mime\InvitationEmail;
use Softspring\UserBundle\Mime\ResetPasswordEmail;
use Softspring\UserBundle\Mime\Example\Model\ExampleInvitation;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserEmailsTest extends TestCase
{
    public function testConfirmationEmailStoresContextAndTranslationParams(): void
    {
        $user = $this->createUser();

        $email = new ConfirmationEmail($user, 'https://example.com/confirm', $this->createTranslator(), 'es');

        $this->assertSame($user, $email->getContext()['user']);
        $this->assertSame('https://example.com/confirm', $email->getContext()['confirmationUrl']);
        $this->assertSame('@SfsUser/register/confirmation.email.twig', $email->getHtmlTemplate());
        $this->assertSame('es', $email->getLocale());
        $this->assertSame([
            '%name%' => 'Ada',
            '%surname%' => 'Lovelace',
            '%username%' => 'ada@example.com',
            '%email%' => 'ada@example.com',
            '%confirmation_url%' => 'https://example.com/confirm',
        ], $email->getTranslationParams());
        $this->assertSame('register.confirm.email.subject', $email->getSubject());
    }

    public function testResetPasswordEmailStoresContextAndTranslationParams(): void
    {
        $user = $this->createUser();

        $email = new ResetPasswordEmail($user, 'https://example.com/reset', $this->createTranslator(), 'ca');

        $this->assertSame($user, $email->getContext()['user']);
        $this->assertSame('https://example.com/reset', $email->getContext()['resetUrl']);
        $this->assertSame('@SfsUser/reset_password/request.email.twig', $email->getHtmlTemplate());
        $this->assertSame('ca', $email->getLocale());
        $this->assertSame('https://example.com/reset', $email->getTranslationParams()['%reset_url%']);
        $this->assertSame('reset_password.email.subject', $email->getSubject());
    }

    public function testInvitationEmailStoresContextAndTranslationParams(): void
    {
        $invitation = new ExampleInvitation();
        $invitation->setEmail('grace@example.com');
        $invitation->setName('Grace');
        $invitation->setSurname('Hopper');

        $email = new InvitationEmail($invitation, 'https://example.com/accept', $this->createTranslator(), 'en');

        $this->assertSame($invitation, $email->getContext()['invitation']);
        $this->assertSame('https://example.com/accept', $email->getContext()['acceptUrl']);
        $this->assertSame('@SfsUser/invitation/invite.email.twig', $email->getHtmlTemplate());
        $this->assertSame([
            '%email%' => 'grace@example.com',
            '%accept_url%' => 'https://example.com/accept',
            '%name%' => 'Grace',
            '%surname%' => 'Hopper',
        ], $email->getTranslationParams());
        $this->assertSame('invitation.email.subject', $email->getSubject());
    }

    public function testExampleEmailsCanBeGenerated(): void
    {
        $translator = $this->createTranslator();

        $this->assertInstanceOf(ConfirmationEmail::class, ConfirmationEmail::generateExample($translator));
        $this->assertInstanceOf(InvitationEmail::class, InvitationEmail::generateExample($translator));
        $this->assertInstanceOf(ResetPasswordEmail::class, ResetPasswordEmail::generateExample($translator));
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
