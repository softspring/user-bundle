<?php

namespace Softspring\UserBundle\Mailer;

use Softspring\UserBundle\Mailer\Exception\InvalidUserClassException;
use Softspring\UserBundle\Model\PasswordRequestInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

interface UserMailerInterface
{
    /**
     * Send an email to a user to confirm the user creation
     *
     * @param UserInterface $user
     * @param string|null   $locale
     *
     * @throws TransportExceptionInterface
     * @throws InvalidUserClassException
     */
    public function sendRegisterConfirmationEmail(UserInterface $user, ?string $locale = null): void;

    /**
     * Send an invitation email to a user
     *
     * @param UserInvitationInterface $invitation
     * @param string|null             $locale
     *
     * @throws TransportExceptionInterface
     */
    public function sendInvitationEmail(UserInvitationInterface $invitation, ?string $locale = null): void;

    /**
     * Send an email to a user with the password reset link
     *
     * @param UserInterface|PasswordRequestInterface $user
     * @param string|null                            $locale
     *
     * @throws TransportExceptionInterface
     * @throws InvalidUserClassException
     */
    public function sendResettingEmail(UserInterface $user, ?string $locale = null): void;
}