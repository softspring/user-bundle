<?php

namespace Softspring\UserBundle\EventListener\Admin;

use Psr\EventDispatcher\EventDispatcherInterface;
use Softspring\CrudlBundle\Event\GetResponseEntityEvent;
use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvitationControllerListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            SfsUserEvents::ADMIN_INVITATIONS_CREATE_SUCCESS => 'onInvitationLaunchEvent',
        ];
    }

    public function onInvitationLaunchEvent(GetResponseEntityEvent $event, string $eventName, EventDispatcherInterface $dispatcher): void
    {
        $dispatcher->dispatch(new UserInvitationEvent($event->getEntity(), $event->getRequest()), SfsUserEvents::USER_INVITED);
    }
}
