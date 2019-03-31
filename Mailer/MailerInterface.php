<?php

namespace Softspring\UserBundle\Mailer;

use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;

interface MailerInterface
{
    /**
     * Send an email to a user to confirm the user creation
     *
     * @param UserInterface $user
     */
    public function sendConfirmationEmail(UserInterface $user);

    /**
     * Send an invitation email to a user
     *
     * @param UserInvitationInterface $invitation
     */
    public function sendInvitationEmail(UserInvitationInterface $invitation);

    /**
     * Send an email to a user with the password reset link
     *
     * @param UserInterface $user
     */
    public function sendResettingEmail(UserInterface $user);
}