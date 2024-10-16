<?php

namespace Softspring\UserBundle\EventListener;

use Exception;
use Softspring\UserBundle\Event\UserEvent;
use Softspring\UserBundle\Manipulator\UserAccessManipulator;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserAccessEventSubscriber implements EventSubscriberInterface
{
    protected UserAccessManipulator $userAccessManipulator;

    public function __construct(UserAccessManipulator $userAccessManipulator)
    {
        $this->userAccessManipulator = $userAccessManipulator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SfsUserEvents::LOGIN_IMPLICIT => 'onImplicitLogin',
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }

    /**
     * @throws Exception
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
     * @throws Exception
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $request = $event->getRequest();

        if ($user instanceof UserInterface) {
            $this->userAccessManipulator->register($user, $request);
        }
    }
}
