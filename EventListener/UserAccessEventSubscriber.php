<?php

namespace Softspring\UserBundle\EventListener;

use Softspring\UserBundle\Event\UserEvent;
use Softspring\UserBundle\Manipulator\UserAccessManipulator;
use Softspring\User\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserAccessEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserAccessManipulator
     */
    protected $userAccessManipulator;

    /**
     * UserAccessEventSubscriber constructor.
     *
     * @param UserAccessManipulator $userAccessManipulator
     */
    public function __construct(UserAccessManipulator $userAccessManipulator)
    {
        $this->userAccessManipulator = $userAccessManipulator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            SfsUserEvents::LOGIN_IMPLICIT => 'onImplicitLogin',
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        );
    }

    /**
     * @param UserEvent $event
     *
     * @throws \Exception
     */
    public function onImplicitLogin(UserEvent $event)
    {
        $user = $event->getUser();
        $request = $event->getRequest();

        if ($request instanceof Request) {
            $this->userAccessManipulator->register($user, $request);
        }
    }

    /**
     * @param InteractiveLoginEvent $event
     *
     * @throws \Exception
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $request = $event->getRequest();

        if ($user instanceof UserInterface && $request instanceof Request) {
            $this->userAccessManipulator->register($user, $request);
        }
    }
}