<?php

namespace Softspring\UserBundle\EventListener;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\Mailer\UserMailerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailInvitationListener implements EventSubscriberInterface
{
    protected UserMailerInterface $mailer;

    protected LoggerInterface $logger;

    public function __construct(UserMailerInterface $mailer, ?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
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
        try {
            $this->mailer->sendInvitationEmail($invitation);
        } catch (Exception $e) {
            $this->logger->error('Failed to send invitation email.', ['exception' => $e, 'invitation_id' => $invitation->getId()]);
        }
    }
}
