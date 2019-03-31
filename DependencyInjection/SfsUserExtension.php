<?php

namespace Softspring\UserBundle\DependencyInjection;

use Softspring\UserBundle\Entity\Base\User;
use Softspring\UserBundle\Entity\Invite\UserInvitation;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SfsUserExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        // set config parameters
        $container->setParameter('sfs_user.user.class', $config['class']);
        $container->setParameter('sfs_user.invite.class', $config['invite']['class']);
        $container->setParameter('sfs_user.history.config', $config['history']);
        $container->setParameter('sfs_user.resetting.config', $config['resetting']);

        // load default mappings
        if ($config['class'] === User::class) {
            $container->setParameter('sfs_user.user.load_user_default_mapping', true);
        }
        if ($config['invite']['class'] === UserInvitation::class) {
            $container->setParameter('sfs_user.user.load_user_invitation_default_mapping', true);
        }
        if ($config['history']['enabled']) {
            $container->setParameter('sfs_user.user.load_history_default_mapping', true);
        }

        // load services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $doctrineConfig = [];

        // add a default config to force load target_entities, will be overwritten by ResolveDoctrineTargetEntityPass
        $doctrineConfig['orm']['resolve_target_entities'][UserInterface::class] = User::class;

        // disable auto-mapping for this bundle to prevent mapping errors
        $doctrineConfig['orm']['mappings']['SfsUserBundle'] = [
            'is_bundle' => true,
            'mapping' => false,
        ];

        $container->prependExtensionConfig('doctrine', $doctrineConfig);
    }
}