<?php

namespace Softspring\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sfs_user');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('class')
                    ->defaultValue('App\Entity\User')
                ->end()

                ->scalarNode('entity_manager')
                    ->defaultValue('default')
                ->end()

                ->arrayNode('login')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('target_path_parameter')->defaultNull()->end()
                        ->arrayNode('google_identity_platform')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('client_id')->defaultValue('')->end()
                                ->scalarNode('api_key')->defaultValue('')->end()
                                ->scalarNode('tenant_id')->defaultNull()->end()
                                ->scalarNode('context')->defaultValue('signin')->end()
                                ->scalarNode('ux_mode')->defaultValue('redirect')->end()
                                ->booleanNode('auto_prompt')->defaultTrue()->end()
                                ->booleanNode('itp_support')->defaultTrue()->end()
                                ->scalarNode('success_route')->defaultValue('sfs_user_preferences')->end()
                                ->arrayNode('success_route_params')->treatNullLike([])->scalarPrototype()->end()->end()
                                ->scalarNode('failure_route')->defaultValue('sfs_user_login')->end()
                                ->arrayNode('failure_route_params')->treatNullLike([])->scalarPrototype()->end()->end()
                                ->arrayNode('button')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('type')->defaultValue('standard')->end()
                                        ->scalarNode('theme')->defaultValue('outline')->end()
                                        ->scalarNode('size')->defaultValue('large')->end()
                                        ->scalarNode('shape')->defaultValue('rectangular')->end()
                                        ->scalarNode('text')->defaultValue('continue_with')->end()
                                        ->scalarNode('logo_alignment')->defaultValue('left')->end()
                                        ->integerNode('width')->defaultValue(320)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('invite')
                    ->canBeEnabled()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('class')->defaultValue('App\\Entity\\UserInvitation')->end()
                    ->end()
                ->end()

                ->arrayNode('mailer')
                    ->children()
                        ->arrayNode('from')
                            ->children()
                                ->scalarNode('address')->end()
                                ->scalarNode('name')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('history')
                    ->canBeEnabled()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('class')->defaultValue('App\\Entity\\UserAccess')->end()
                    ->end()
                ->end()

                ->arrayNode('confirm_email')
                    ->canBeEnabled()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()

                ->arrayNode('reset_password')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('token_ttl')->defaultValue(3600 * 24)->end()
                    ->end()
                ->end()

                ->arrayNode('impersonate_bar')
                    ->canBeEnabled()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('switch_role')->defaultValue('ROLE_ALLOWED_TO_SWITCH')->end()
                        ->scalarNode('switch_route')->defaultValue('configure_switch_route')->end()
                        ->arrayNode('switch_route_params')->treatNullLike([])->scalarPrototype()->end()->end()
                        ->scalarNode('switch_parameter')->defaultValue('_switch_user')->end()
                    ->end()
                ->end()

                ->arrayNode('oauth')
                    ->children()
                        ->arrayNode('facebook')
                            ->children()
                                ->scalarNode('application_id')->end()
                                ->scalarNode('application_secret')->end()
                                ->booleanNode('login_create')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
