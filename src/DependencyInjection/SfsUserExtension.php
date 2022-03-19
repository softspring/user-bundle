<?php

namespace Softspring\UserBundle\DependencyInjection;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Softspring\MailerBundle\Template\Loader\TemplateLoaderInterface;
use Softspring\UserBundle\Model\UserInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SfsUserExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        // set config parameters
        $container->setParameter('sfs_user.entity_manager_name', $config['entity_manager']);

        $container->setParameter('sfs_user.user.class', $config['class']);

        $container->setParameter('sfs_user.invite.enabled', $config['invite']['enabled']);
        $container->setParameter('sfs_user.invite.class', $config['invite']['enabled'] ? $config['invite']['class'] : null);

        $container->setParameter('sfs_user.impersonate_bar', $config['impersonate_bar']);

        $container->setParameter('sfs_user.history.enabled', $config['history']['enabled']);
        $container->setParameter('sfs_user.history.class', $config['history']['enabled'] ? $config['history']['class'] : null);

        $container->setParameter('sfs_user.reset_password.token_ttl', $config['reset_password']['token_ttl']);
        $container->setParameter('sfs_user.login.target_path_parameter', $config['login']['target_path_parameter']);

        // load services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');

        if ($config['history']['enabled']) {
            $loader->load('services/history.yaml');
            $loader->load('services/controller/admin_history.yaml');
        }

        if ($config['impersonate_bar']['enabled']) {
            $loader->load('services/impersonate.yaml');
        }

        $loader->load('services/controller/admin_administrators.yaml');
        $loader->load('services/controller/admin_administrators_invite.yaml');
        $loader->load('services/controller/admin_users.yaml');

        if ($config['invite']['enabled']) {
            $loader->load('services/controller/admin_invitations.yaml');
        }

        $loader->load('services/controller/settings_change_email.yaml');
        $loader->load('services/controller/settings_change_password.yaml');

        $oauthServicesConfig = $config['oauth'] ?? [];
        $container->setParameter('sfs_user.oauth.services', $oauthServicesConfig);

        if (!empty($oauthServicesConfig)) {
            if (!class_exists('HWI\Bundle\OAuthBundle\HWIOAuthBundle')) {
                throw new InvalidConfigurationException('Oauth features requires HWIOAuthBundle, see documentation.');
            }

            foreach ($oauthServicesConfig as $service => $serviceConfig) {
                foreach ($serviceConfig as $field => $value) {
                    $container->setParameter("sfs_user.oauth.$service.$field", $value);
                }
            }

            $loader->load('services/oauth.yaml');
        }

        if (interface_exists(TemplateLoaderInterface::class)) {
            $loader->load('services/mailer_loader.yaml');
        }

        if (class_exists(Fixture::class)) {
            $loader->load('services/data_fixtures.yaml');
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

        $container->prependExtensionConfig('sfs_twig_extra', [
            'routing_extension' => true,
            'date_span_extension' => true,
        ]);
    }
}
