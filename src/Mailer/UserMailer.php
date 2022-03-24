<?php

namespace Softspring\UserBundle\Mailer;

use Softspring\UserBundle\Mailer\Exception\InvalidUserClassException;
use Softspring\UserBundle\Mime\ConfirmationEmail;
use Softspring\UserBundle\Mime\InvitationEmail;
use Softspring\UserBundle\Mime\ResetPasswordEmail;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\PasswordRequestInterface;
use Softspring\UserBundle\Model\UserHasLocalePreferenceInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserMailer implements UserMailerInterface
{
    protected MailerInterface $mailer;

    protected UrlGeneratorInterface $urlGenerator;

    protected TranslatorInterface $translator;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function sendRegisterConfirmationEmail(UserInterface $user, ?string $locale = null): void
    {
        if (!$user instanceof ConfirmableInterface) {
            throw new InvalidUserClassException(sprintf('%s must implements %s interface', get_class($user), ConfirmableInterface::class));
        }

        if (!$user instanceof UserWithEmailInterface) {
            throw new InvalidUserClassException(sprintf('%s must implements %s interface', get_class($user), UserWithEmailInterface::class));
        }

        $toName = $user instanceof NameSurnameInterface ? implode(' ', [$user->getName(), $user->getSurname()]) : '';

        $locale = $user && $user instanceof UserHasLocalePreferenceInterface ? $user->getLocale() : $locale;

        $confirmationUrl = $this->urlGenerator->generate('sfs_user_register_confirm', [
            'user' => $user->getId(),
            'token' => $user->getConfirmationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new ConfirmationEmail($user, $confirmationUrl, $this->translator, $locale))
            ->to(new Address($user->getEmail(), $toName))
        ;

        $this->mailer->send($email);
    }

    public function sendInvitationEmail(UserInvitationInterface $invitation, ?string $locale = null): void
    {
        $toName = $invitation instanceof NameSurnameInterface ? "{$invitation->getName()} {$invitation->getSurname()}" : '';
        $acceptUrl = $this->urlGenerator->generate('sfs_user_invitation_accept', ['token' => $invitation->getInvitationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new InvitationEmail($invitation, $acceptUrl, $this->translator, $locale))
            ->to(new Address($invitation->getEmail(), $toName))
        ;

        $this->mailer->send($email);
    }

    public function sendResettingEmail(UserInterface $user, ?string $locale = null): void
    {
        if (!$user instanceof PasswordRequestInterface) {
            throw new InvalidUserClassException(sprintf('%s must implements %s interface', get_class($user), PasswordRequestInterface::class));
        }

        if (!$user instanceof UserWithEmailInterface) {
            throw new InvalidUserClassException(sprintf('%s must implements %s interface', get_class($user), UserWithEmailInterface::class));
        }

        $toName = $user instanceof NameSurnameInterface ? implode(' ', [$user->getName(), $user->getSurname()]) : '';

        $resetUrl = $this->urlGenerator->generate('sfs_user_reset_password', [
            'user' => $user->getId(),
            'token' => $user->getPasswordRequestToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $locale = $user && $user instanceof UserHasLocalePreferenceInterface ? $user->getLocale() : $locale;

        $email = (new ResetPasswordEmail($user, $resetUrl, $this->translator, $locale))
            ->to(new Address($user->getEmail(), $toName))
        ;

        $this->mailer->send($email);
    }
}
