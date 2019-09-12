<?php

namespace Softspring\UserBundle\EventListener;

use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Event\UserEvent;
use Softspring\UserBundle\Security\LoginManager;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AutoAuthenticateEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoginManager
     */
    protected $loginManager;

    /**
     * AuthenticateEventSubscriber constructor.
     *
     * @param LoginManager $loginManager
     */
    public function __construct(LoginManager $loginManager)
    {
        $this->loginManager = $loginManager;
    }

    /**
     * @inheritdoc
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

    /**
     * @param GetResponseUserEvent     $event
     * @param string                   $eventName
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function authenticate(GetResponseUserEvent $event, string $eventName, EventDispatcherInterface $eventDispatcher)
    {
        if (!$event->getUser()->isEnabled()) {
            return;
        }

        $this->loginManager->loginUser($event->getRequest(), $event->getUser(), $event->getResponse());

        $eventDispatcher->dispatch(new UserEvent($event->getUser(), $event->getRequest()), SfsUserEvents::LOGIN_IMPLICIT);
    }
}