<?php

namespace Softspring\UserBundle\DependencyInjection\Compiler;

use Softspring\User\Model\UserAccessInterface;
use Softspring\User\Model\UserInterface;
use Softspring\User\Model\UserInvitationInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;

class ResolveDoctrineTargetEntityPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // configure user entity
        $userClass = $container->getParameter('sfs_user.user.class');
        if (!class_implements($userClass, UserInterface::class)) {
            throw new LogicException(sprintf('%s class must implements %s interface', $userClass, UserInterface::class));
        }
        $this->setTargetEntity($container, UserInterface::class, $userClass);

        // configure user entity
        $userInvitationClass = $container->getParameter('sfs_user.invite.class');
        if (!class_implements($userInvitationClass, UserInvitationInterface::class)) {
            throw new LogicException(sprintf('%s class must implements %s interface', $userInvitationClass, UserInvitationInterface::class));
        }
        $this->setTargetEntity($container, UserInvitationInterface::class, $userInvitationClass);

        $historyConfig = $container->getParameter('sfs_user.history.config');
        if ($historyConfig['enabled']) {
            if (!class_implements($historyConfig['class'], UserAccessInterface::class)) {
                throw new LogicException(sprintf('%s class must implements %s interface', $historyConfig['class'], UserAccessInterface::class));
            }
            $this->setTargetEntity($container, UserAccessInterface::class, $historyConfig['class']);
        }
    }

    private function setTargetEntity(ContainerBuilder $container, string $interface, string $class)
    {
        $resolveTargetEntityListener = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');

        if (!$resolveTargetEntityListener->hasTag('doctrine.event_subscriber')) {
            $resolveTargetEntityListener->addTag('doctrine.event_subscriber');
        }

        $resolveTargetEntityListener->addMethodCall('addResolveTargetEntity', [$interface, $class, []]);
    }
}