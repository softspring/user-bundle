<?php

namespace Softspring\UserBundle\EventListener\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Softspring\AccountBundle\Model\UserMultiAccountedInterface;
use Softspring\Component\CrudlController\Event\GetResponseEntityEvent;
use Softspring\Component\Events\ViewEvent;
use Softspring\UserBundle\Doctrine\Filter\AdminFilter;
use Softspring\UserBundle\Event\GetResponseUserEvent;
use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AdministratorControllerListener implements EventSubscriberInterface
{
    protected EntityManagerInterface $em;

    protected RouterInterface $router;

    protected array $impersonateBarConfig;

    protected ?UserAccessManagerInterface $accessManager;

    protected TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $em, RouterInterface $router, array $impersonateBarConfig, ?UserAccessManagerInterface $accessManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->router = $router;
        $this->impersonateBarConfig = $impersonateBarConfig;
        $this->accessManager = $accessManager;
        $this->tokenStorage = $tokenStorage;
    }

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
            SfsUserEvents::ADMIN_USERS_PROMOTE_SUCCESS => 'onUserPromoteRedirect',
            SfsUserEvents::ADMIN_ADMINISTRATORS_DEMOTE_SUCCESS => 'onAdminDemoteRedirect',
            SfsUserEvents::ADMIN_ADMINISTRATORS_UPDATE_SUCCESS => 'onUpdateSuccessRedirect',
            SfsUserEvents::ADMIN_ADMINISTRATORS_PROMOTE_SUPER_SUCCESS => 'onAdminPromoteSuperRedirect',
            SfsUserEvents::ADMIN_ADMINISTRATORS_DEMOTE_SUPER_SUCCESS => 'onAdminDemoteSuperRedirect',
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

        $data['multi_accounted_user'] = $data['administrator'] instanceof UserMultiAccountedInterface;
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

    public function onUserPromoteRedirect(GetResponseUserEvent $event): void
    {
        $event->setResponse(new RedirectResponse($this->router->generate('sfs_user_admin_administrators_details', ['administrator' => $event->getUser()])));
    }

    public function onAdminDemoteRedirect(GetResponseUserEvent $event): void
    {
        $event->setResponse(new RedirectResponse($this->router->generate('sfs_user_admin_users_details', ['user' => $event->getUser()])));
    }

    public function onAdminPromoteSuperRedirect(GetResponseUserEvent $event): void
    {
        $event->setResponse(new RedirectResponse($this->router->generate('sfs_user_admin_administrators_details', ['administrator' => $event->getUser()])));
    }

    public function onAdminDemoteSuperRedirect(GetResponseUserEvent $event): void
    {
        $event->setResponse(new RedirectResponse($this->router->generate('sfs_user_admin_administrators_details', ['administrator' => $event->getUser()])));
    }

    public function onUpdateSuccessRedirect(GetResponseEntityEvent $event): void
    {
        $event->setResponse(new RedirectResponse($this->router->generate('sfs_user_admin_administrators_details', ['administrator' => $event->getEntity()])));
    }
}
