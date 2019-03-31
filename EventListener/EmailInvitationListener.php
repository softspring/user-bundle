<?php

namespace Softspring\UserBundle\EventListener;

use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\Mailer\MailerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailInvitationListener implements EventSubscriberInterface
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * EmailInvitationListener constructor.
     *
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            SfsUserEvents::USER_INVITED => 'onInvitation',
        );
    }

    /**
     * @param UserInvitationEvent $event
     */
    public function onInvitation(UserInvitationEvent $event)
    {
        $invitation = $event->getInvitation();

        $this->mailer->sendInvitationEmail($invitation);
    }
}
