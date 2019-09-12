<?php

namespace Softspring\UserBundle\Mailer;

use Softspring\MailerBundle\Mailer\TemplateMailer;
use Softspring\UserBundle\Model\PasswordRequestInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MailerTemplate implements MailerInterface
{
    /**
     * @var TemplateMailer
     */
    protected $templateMailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * MailerTemplate constructor.
     *
     * @param TemplateMailer        $templateMailer
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(TemplateMailer $templateMailer, UrlGeneratorInterface $urlGenerator)
    {
        $this->templateMailer = $templateMailer;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritdoc
     */
    public function sendConfirmationEmail(UserInterface $user)
    {
        // TODO: Implement sendConfirmationEmail() method.
    }

    /**
     * @inheritdoc
     *
     * @throws \Softspring\MailerBundle\Exception\InvalidTemplateException
     * @throws \Softspring\MailerBundle\Exception\TemplateRenderException
     */
    public function sendInvitationEmail(UserInvitationInterface $invitation)
    {
        $toName = $invitation->getName() ? "{$invitation->getName()} {$invitation->getSurname()}" : null;

        $acceptUrl = $this->urlGenerator->generate('sfs_user_invite_accept', array('token' => $invitation->getInvitationToken()), UrlGeneratorInterface::ABSOLUTE_URL);

        $this->templateMailer->send('sfs_user.invite', 'es', [
            'user_name' => $invitation->getName(),
            'user_surname' => $invitation->getSurname(),
            'user_username' => $invitation->getUsername(),
            'user_email' => $invitation->getEmail(),
            'acceptUrl' => $acceptUrl,
        ], $invitation->getEmail(), $toName);
    }

    /**
     * @param UserInterface|PasswordRequestInterface $user
     *
     * @throws \Softspring\MailerBundle\Exception\InvalidTemplateException
     * @throws \Softspring\MailerBundle\Exception\TemplateRenderException
     */
    public function sendResettingEmail(UserInterface $user)
    {
        $name = method_exists($user, 'getName') ? $user->getName() : '';
        $surname = method_exists($user, 'getSurname') ? $user->getSurname() : '';

        $resetUrl = $this->urlGenerator->generate('sfs_user_reset_password', [
            'user' => $user->getId(),
            'token' => $user->getPasswordRequestToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->templateMailer->send('sfs_user.reset_password', 'es', [
            'user_name' => $name,
            'user_surname' => $surname,
            'user_username' => $user->getUsername(),
            'user_email' => $user->getEmail(),
            'resetUrl' => $resetUrl,
        ], $user->getEmail(), "$name $surname");
    }
}