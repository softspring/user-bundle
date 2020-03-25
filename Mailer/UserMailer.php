<?php

namespace Softspring\UserBundle\Mailer;

use Softspring\UserBundle\Mailer\Exception\InvalidUserClassException;
use Softspring\UserBundle\Mime\ConfirmationEmail;
use Softspring\UserBundle\Mime\InvitationEmail;
use Softspring\UserBundle\Mime\ResetPasswordEmail;
use Softspring\UserBundle\Model\ConfirmableInterface;
use Softspring\UserBundle\Model\NameSurnameInterface;
use Softspring\UserBundle\Model\PasswordRequestInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Softspring\UserBundle\Model\UserWithEmailInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserMailer implements UserMailerInterface
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * UserMailer constructor.
     *
     * @param MailerInterface       $mailer
     * @param UrlGeneratorInterface $urlGenerator
     * @param TranslatorInterface   $translator
     */
    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    public function sendRegisterConfirmationEmail(UserInterface $user, ?string $locale = null): void
    {
        if (! $user instanceof ConfirmableInterface) {
            throw new InvalidUserClassException(sprintf('%s must implements %s interface', get_class($user), ConfirmableInterface::class));
        }

        if (! $user instanceof UserWithEmailInterface) {
            throw new InvalidUserClassException(sprintf('%s must implements %s interface', get_class($user), UserWithEmailInterface::class));
        }

        $toName = $user instanceof NameSurnameInterface ? implode(' ', [$user->getName(), $user->getSurname()]) : '';

        $confirmationUrl = $this->urlGenerator->generate('sfs_user_register_confirm', [
            'user' => $user->getId(),
            'token' => $user->getConfirmationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new ConfirmationEmail($user, $confirmationUrl, $this->translator, $locale))
            ->from('development@softspring.eu')
            ->to(new Address($user->getEmail(), $toName))
        ;

        $this->mailer->send($email);
    }

    /**
     * @inheritdoc
     */
    public function sendInvitationEmail(UserInvitationInterface $invitation, ?string $locale = null): void
    {
        $toName = $invitation->getName() ? "{$invitation->getName()} {$invitation->getSurname()}" : null;
        $acceptUrl = $this->urlGenerator->generate('sfs_user_invite_accept', ['token' => $invitation->getInvitationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new InvitationEmail($invitation, $acceptUrl, $this->translator, $locale))
            ->from('development@softspring.eu')
            ->to(new Address($invitation->getEmail(), $toName))
        ;

        $this->mailer->send($email);
    }

    /**
     * @inheritdoc
     */
    public function sendResettingEmail(UserInterface $user, ?string $locale = null): void
    {
        if (! $user instanceof PasswordRequestInterface) {
            throw new InvalidUserClassException(sprintf('%s must implements %s interface', get_class($user), PasswordRequestInterface::class));
        }

        if (! $user instanceof UserWithEmailInterface) {
            throw new InvalidUserClassException(sprintf('%s must implements %s interface', get_class($user), UserWithEmailInterface::class));
        }

        $toName = $user instanceof NameSurnameInterface ? implode(' ', [$user->getName(), $user->getSurname()]) : '';

        $resetUrl = $this->urlGenerator->generate('sfs_user_reset_password', [
            'user' => $user->getId(),
            'token' => $user->getPasswordRequestToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new ResetPasswordEmail($user, $resetUrl, $this->translator, $locale))
            ->from('development@softspring.eu')
            ->to(new Address($user->getEmail(), $toName))
        ;

        $this->mailer->send($email);
    }
}