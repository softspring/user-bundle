<?php

namespace Softspring\UserBundle\DependencyInjection;

use Softspring\User\Model\UserInterface;
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
        $container->setParameter('sfs_user.entity_manager_name', $config['entity_manager']);

        $container->setParameter('sfs_user.user.class', $config['class']);
        $container->setParameter('sfs_user.invite.class', $config['invite']['class']);

        $container->setParameter('sfs_user.impersonate_bar', $config['impersonate_bar']);

        $container->setParameter('sfs_user.history.enabled', $config['history']['enabled']);
        $container->setParameter('sfs_user.history.class', $config['history']['enabled'] ? $config['history']['class'] : null);

        $container->setParameter('sfs_user.reset_password.token_ttl', $config['reset_password']['token_ttl']);

        // load services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        if ($config['history']['enabled']) {
            $loader->load('services/history.yaml');
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $doctrineConfig = [];

        // add a default config to force load target_entities, will be overwritten by ResolveDoctrineTargetEntityPass
        $doctrineConfig['orm']['resolve_target_entities'][UserInterface::class] = 'App\Entity\User';

        // disable auto-mapping for this bundle to prevent mapping errors
        $doctrineConfig['orm']['mappings']['SfsUserBundle'] = [
            'is_bundle' => true,
            'mapping' => false,
        ];

        $container->prependExtensionConfig('doctrine', $doctrineConfig);
    }
}