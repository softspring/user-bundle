<?php

namespace Softspring\UserBundle\EventListener;

use Softspring\UserBundle\Event\UserEvent;
use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\UserLastLoginInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LastLoginEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * LastLoginEventSubscriber constructor.
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SfsUserEvents::LOGIN_IMPLICIT => 'onImplicitLogin',
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }

    /**
     * @throws \Exception
     */
    public function onImplicitLogin(UserEvent $event)
    {
        $user = $event->getUser();

        if ($user instanceof UserLastLoginInterface) {
            $user->setLastLogin(new \DateTime());
            $this->userManager->saveEntity($user);
        }
    }

    /**
     * @throws \Exception
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof UserLastLoginInterface) {
            $user->setLastLogin(new \DateTime());
            $this->userManager->saveEntity($user);
        }
    }
}
