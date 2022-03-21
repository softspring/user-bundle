<?php

namespace Softspring\UserBundle\Mailer;

use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserMailer implements UserMailerInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var UserMailerInterface
     */
    protected $mailer;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(UrlGeneratorInterface $urlGenerator, MailerInterface $mailer, TranslatorInterface $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * @param UserInterface $user
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendRegisterConfirmationEmail(UserInterface $user)
    {
        $name = method_exists($user, 'getName') ? $user->getName() : '';
        $surname = method_exists($user, 'getSurname') ? $user->getSurname() : '';

        $confirmationUrl = $this->urlGenerator->generate('sfs_user_register_confirm', [
            'user' => $user->getId(),
            'token' => $user->getConfirmationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $mail = (new TemplatedEmail())
            ->htmlTemplate('@SfsUser/register/confirm_email.html.twig')
            ->textTemplate('@SfsUser/register/confirm_email.txt.twig')
            ->subject($this->translator->trans('register.confirm.email.subject', [
                '%username%'=> $user->getUsername(),
                '%name%'=> $name,
                '%surname%'=> $surname,
                '%email%'=> $user->getEmail(),
            ], 'sfs_user'))
            ->context([
                'user_name' => $name,
                'user_surname' => $surname,
                'user_username' => $user->getUsername(),
                'user_email' => $user->getEmail(),
                'confirmationUrl' => $confirmationUrl,
            ])
            ->to(new Address($user->getEmail(), "$name $surname"))
        ;

        $this->mailer->send($mail);
    }

    /**
     * @param UserInvitationInterface $invitation
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendInvitationEmail(UserInvitationInterface $invitation)
    {
        $name = method_exists($invitation, 'getName') ? $invitation->getName() : '';
        $surname = method_exists($invitation, 'getSurname') ? $invitation->getSurname() : '';
        $toName = $invitation->getName() ? "{$invitation->getName()} {$invitation->getSurname()}" : null;

        $acceptUrl = $this->urlGenerator->generate('sfs_user_invite_accept', array('token' => $invitation->getInvitationToken()), UrlGeneratorInterface::ABSOLUTE_URL);

        $mail = (new TemplatedEmail())
            ->htmlTemplate('@SfsUser/invite/invite_email.html.twig')
            ->textTemplate('@SfsUser/invite/invite_email.txt.twig')
            ->subject($this->translator->trans('invite.accept.email.subject', [
                '%username%'=> $invitation->getUsername(),
                '%name%'=> $name,
                '%surname%'=> $surname,
                '%email%'=> $invitation->getEmail(),
            ], 'sfs_user'))
            ->context([
                'user_name' => $invitation->getName(),
                'user_surname' => $invitation->getSurname(),
                'user_username' => $invitation->getUsername(),
                'user_email' => $invitation->getEmail(),
                'acceptUrl' => $acceptUrl,
            ])
            ->to(new Address($invitation->getEmail(), $toName))
        ;

        $this->mailer->send($mail);
    }

    /**
     * @param UserInterface $user
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendResettingEmail(UserInterface $user)
    {
        $name = method_exists($user, 'getName') ? $user->getName() : '';
        $surname = method_exists($user, 'getSurname') ? $user->getSurname() : '';

        $resetUrl = $this->urlGenerator->generate('sfs_user_reset_password', [
            'user' => $user->getId(),
            'token' => $user->getPasswordRequestToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $mail = (new TemplatedEmail())
            ->htmlTemplate('@SfsUser/reset_password/resetting_email.html.twig')
            ->textTemplate('@SfsUser/reset_password/resetting_email.txt.twig')
            ->subject($this->translator->trans('reset_password.email.subject', [
                '%username%'=> $user->getUsername(),
                '%name%'=> $name,
                '%surname%'=> $surname,
                '%email%'=> $user->getEmail(),
            ], 'sfs_user'))
            ->context([
                'user_name' => $name,
                'user_surname' => $surname,
                'user_username' => $user->getUsername(),
                'user_email' => $user->getEmail(),
                'resetUrl' => $resetUrl,
            ])
            ->to(new Address($user->getEmail(), "$name $surname"))
        ;

        $this->mailer->send($mail);
    }
}