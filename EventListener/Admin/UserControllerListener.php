<?php

namespace Softspring\UserBundle\EventListener\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Softspring\CoreBundle\Event\ViewEvent;
use Softspring\UserBundle\Doctrine\Filter\UserFilter;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserControllerListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var array
     */
    protected $impersonateBarConfig;

    /**
     * UserControllerListener constructor.
     *
     * @param EntityManagerInterface $em
     * @param array                  $impersonateBarConfig
     */
    public function __construct(EntityManagerInterface $em, array $impersonateBarConfig)
    {
        $this->em = $em;
        $this->impersonateBarConfig = $impersonateBarConfig;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SfsUserEvents::ADMIN_USERS_DETAILS_VIEW => 'onDetailsViewAddSwitchUserConfiguration',

            // enable filter for users (not admins)
            SfsUserEvents::ADMIN_USERS_LIST_INITIALIZE => 'onControllerInitializeEnableFilter',
            SfsUserEvents::ADMIN_USERS_DETAILS_INITIALIZE => 'onControllerInitializeEnableFilter',
            SfsUserEvents::ADMIN_USERS_DELETE_INITIALIZE => 'onControllerInitializeEnableFilter',
            SfsUserEvents::ADMIN_USERS_PROMOTE_INITIALIZE => 'onControllerInitializeEnableFilter',
        ];
    }

    public function onControllerInitializeEnableFilter()
    {
        $this->em->getConfiguration()->addFilter('user', UserFilter::class);
        $this->em->getFilters()->enable('user');
    }

    public function onDetailsViewAddSwitchUserConfiguration(ViewEvent $event)
    {
        $data = $event->getData();

        $data['multi_accounted_user'] = $data['user'] instanceof \Softspring\AccountBundle\Model\UserMultiAccountedInterface;
        $data['switch_enabled'] = $this->impersonateBarConfig['enabled'] ?? false;
        $data['switch_role'] = $this->impersonateBarConfig['switch_role'] ?? null;
        $data['switch_route'] = $this->impersonateBarConfig['switch_route'] ?? null;
        $data['switch_route_params'] = $this->impersonateBarConfig['switch_route_params'] ?? null;
        $data['switch_parameter'] = $this->impersonateBarConfig['switch_parameter'] ?? null;
    }
}