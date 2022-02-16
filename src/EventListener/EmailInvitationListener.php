<?php

namespace Softspring\UserBundle\EventListener;

use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailInvitationListener implements EventSubscriberInterface
{
    protected UserMailerInterface $mailer;

    public function __construct(UserMailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SfsUserEvents::USER_INVITED => 'onInvitation',
        ];
    }

    public function onInvitation(UserInvitationEvent $event): void
    {
        $invitation = $event->getInvitation();

        $this->mailer->sendInvitationEmail($invitation);
    }
}
