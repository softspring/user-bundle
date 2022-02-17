<?php

namespace Softspring\UserBundle\EventListener\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Softspring\CoreBundle\Event\ViewEvent;
use Softspring\CrudlBundle\Event\GetResponseEntityEvent;
use Softspring\CrudlBundle\Event\GetResponseFormEvent;
use Softspring\UserBundle\Doctrine\Filter\AdminFilter;
use Softspring\UserBundle\Event\UserInvitationEvent;
use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\Model\RolesAdminInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdministratorControllerListener implements EventSubscriberInterface
{
    protected EntityManagerInterface $em;

    protected array $impersonateBarConfig;

    protected ?UserAccessManagerInterface $accessManager;

    protected TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $em, array $impersonateBarConfig, ?UserAccessManagerInterface $accessManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->impersonateBarConfig = $impersonateBarConfig;
        $this->accessManager = $accessManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SfsUserEvents::ADMIN_ADMINISTRATORS_DETAILS_VIEW => [
                ['onDetailsViewAddMultiAccountedDetails'],
                ['onDetailsViewAddSwitchUserConfiguration'],
                ['onDetailsViewShowHistory'],
            ],

            // enable filter for administrators
            SfsUserEvents::ADMIN_ADMINISTRATORS_LIST_INITIALIZE => 'onControllerInitializeEnableFilter',
            SfsUserEvents::ADMIN_ADMINISTRATORS_DETAILS_INITIALIZE => 'onControllerInitializeEnableFilter',
            SfsUserEvents::ADMIN_ADMINISTRATORS_INVITE_FORM_VALID => 'onAdminInvitationValidSetInviterAndAdmin',
            SfsUserEvents::ADMIN_ADMINISTRATORS_INVITE_SUCCESS => 'onAdminInvitationSuccessLaunchEvent',
        ];
    }

    public function onControllerInitializeEnableFilter()
    {
        $this->em->getConfiguration()->addFilter('administrator', AdminFilter::class);
        $this->em->getFilters()->enable('administrator');
    }

    public function onDetailsViewAddMultiAccountedDetails(ViewEvent $event)
    {
        $data = $event->getData();

        $data['multi_accounted_user'] = $data['administrator'] instanceof \Softspring\AccountBundle\Model\UserMultiAccountedInterface;
    }

    public function onDetailsViewAddSwitchUserConfiguration(ViewEvent $event)
    {
        $data = $event->getData();

        $data['switch_enabled'] = $this->impersonateBarConfig['enabled'] ?? false;
        $data['switch_role'] = $this->impersonateBarConfig['switch_role'] ?? null;
        $data['switch_route'] = $this->impersonateBarConfig['switch_route'] ?? null;
        $data['switch_route_params'] = $this->impersonateBarConfig['switch_route_params'] ?? null;
        $data['switch_parameter'] = $this->impersonateBarConfig['switch_parameter'] ?? null;
    }

    public function onDetailsViewShowHistory(ViewEvent $event)
    {
        if (!$this->accessManager instanceof UserAccessManagerInterface) {
            return;
        }

        $data = $event->getData();

        $data['user_access_history'] = $this->accessManager->getRepository()->findBy(['user' => $data['administrator']], ['loginAt' => 'DESC'], 5);
    }

    public function onAdminInvitationValidSetInviterAndAdmin(GetResponseFormEvent $event, string $eventName, EventDispatcherInterface $dispatcher): void
    {
        /** @var UserInvitationInterface $invitation */
        $invitation = $event->getForm()->getData();
        /** @var UserInterface $inviter */
        $inviter = $this->tokenStorage->getToken()->getUser();
        $invitation->setInviter($inviter);

        if ($invitation instanceof RolesAdminInterface) {
            $invitation->setAdmin(true);
        }
    }

    public function onAdminInvitationSuccessLaunchEvent(GetResponseEntityEvent $event, string $eventName, EventDispatcherInterface $dispatcher): void
    {
        $dispatcher->dispatch(new UserInvitationEvent($event->getEntity(), $event->getRequest()), SfsUserEvents::USER_INVITED);
    }
}
