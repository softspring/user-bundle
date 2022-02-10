<?php

namespace Softspring\UserBundle\EventListener;

use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Event\UserEvent;
use Softspring\UserBundle\Model\EnablableInterface;
use Softspring\UserBundle\Security\LoginManager;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AutoAuthenticateEventSubscriber implements EventSubscriberInterface
{
    protected LoginManager $loginManager;

    /**
     * AuthenticateEventSubscriber constructor.
     */
    public function __construct(LoginManager $loginManager)
    {
        $this->loginManager = $loginManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SfsUserEvents::REGISTER_SUCCESS => [['authenticate', 0]],
            SfsUserEvents::INVITATION_ACCEPTED => [['authenticate', 0]],
            SfsUserEvents::CONFIRMATION_SUCCESS => [['authenticate', 0]],
            SfsUserEvents::RESET_SUCCESS => [['authenticate', 0]],
        ];
    }

    public function authenticate(GetResponseUserEvent $event, string $eventName, EventDispatcherInterface $eventDispatcher)
    {
        $user = $event->getUser();

        if ($user instanceof EnablableInterface && !$user->isEnabled()) {
            return;
        }

        $this->loginManager->loginUser($event->getRequest(), $user, $event->getResponse());

        $eventDispatcher->dispatch(new UserEvent($user, $event->getRequest()), SfsUserEvents::LOGIN_IMPLICIT);
    }
}
