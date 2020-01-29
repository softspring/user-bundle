<?php

namespace Softspring\UserBundle\EventListener\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Softspring\CoreBundle\Event\ViewEvent;
use Softspring\UserBundle\Doctrine\Filter\AdminFilter;
use Softspring\UserBundle\Manager\UserAccessManagerInterface;
use Softspring\UserBundle\SfsUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdministratorControllerListener implements EventSubscriberInterface
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
     * @var UserAccessManagerInterface|null
     */
    protected $accessManager;

    /**
     * AdministratorControllerListener constructor.
     *
     * @param EntityManagerInterface $em
     * @param array                  $impersonateBarConfig
     * @param UserAccessManagerInterface|null $accessManager
     */
    public function __construct(EntityManagerInterface $em, array $impersonateBarConfig, ?UserAccessManagerInterface $accessManager)
    {
        $this->em = $em;
        $this->impersonateBarConfig = $impersonateBarConfig;
        $this->accessManager = $accessManager;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
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
}