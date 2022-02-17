<?php

namespace Softspring\UserBundle\EventListener\Admin;

use Psr\EventDispatcher\EventDispatcherInterface;
use Softspring\CrudlBundle\Event\GetResponseEntityEvent;
use Softspring\CrudlBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class InvitationControllerListener implements EventSubscriberInterface
{
    protected TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SfsUserEvents::ADMIN_INVITATIONS_CREATE_FORM_VALID => 'onInvitationValidSetInviter',
            SfsUserEvents::ADMIN_INVITATIONS_CREATE_SUCCESS => 'onInvitationSuccessLaunchEvent',
        ];
    }

    public function onInvitationSuccessLaunchEvent(GetResponseEntityEvent $event, string $eventName, EventDispatcherInterface $dispatcher): void
    {
        $dispatcher->dispatch(new UserInvitationEvent($event->getEntity(), $event->getRequest()), SfsUserEvents::USER_INVITED);
    }

    public function onInvitationValidSetInviter(GetResponseFormEvent $event, string $eventName, EventDispatcherInterface $dispatcher): void
    {
        /** @var UserInvitationInterface $invitation */
        $invitation = $event->getForm()->getData();
        /** @var UserInterface $inviter */
        $inviter = $this->tokenStorage->getToken()->getUser();
        $invitation->setInviter($inviter);
    }
}
