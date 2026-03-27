<?php

namespace Softspring\UserBundle\Tests\TestApplication;

use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;

class TestUserMailer implements UserMailerInterface
{
    public function sendRegisterConfirmationEmail(UserInterface $user, ?string $locale = null): void
    {
    }

    public function sendInvitationEmail(UserInvitationInterface $invitation, ?string $locale = null): void
    {
    }

    public function sendResettingEmail(UserInterface $user, ?string $locale = null): void
    {
    }
}
