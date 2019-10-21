<?php

namespace Softspring\UserBundle\DependencyInjection\Compiler;

use Softspring\CoreBundle\DependencyInjection\Compiler\AbstractResolveDoctrineTargetEntityPass;
use Softspring\UserBundle\Model\UserAccessInterface;
use Softspring\UserBundle\Model\UserInterface;
use Softspring\UserBundle\Model\UserInvitationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResolveDoctrineTargetEntityPass extends AbstractResolveDoctrineTargetEntityPass
{
    /**
     * @inheritDoc
     */
    protected function getEntityManagerName(ContainerBuilder $container): string
    {
        return $container->getParameter('sfs_user.entity_manager_name');
    }

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->setTargetEntityFromParameter('sfs_user.user.class', UserInterface::class, $container, true);
        $this->setTargetEntityFromParameter('sfs_user.invite.class', UserInvitationInterface::class, $container, false);
        $this->setTargetEntityFromParameter('sfs_user.history.class', UserAccessInterface::class, $container, false);
    }
}