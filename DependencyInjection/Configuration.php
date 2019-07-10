<?php

namespace Softspring\UserBundle\DependencyInjection;

use Softspring\UserBundle\Form\ChangeEmailForm;
use Softspring\UserBundle\Form\ChangePasswordForm;
use Softspring\UserBundle\Form\ChangeUsernameForm;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
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

                ->arrayNode('invite')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultNull()->end()
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
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('class')->defaultNull()->end()
                    ->end()
                ->end()

                ->arrayNode('confirm_email')
                    ->canBeEnabled()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                    ->end()
                ->end()

                ->arrayNode('resetting')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('form')->defaultValue(ChangePasswordForm::class)->end()
                        ->arrayNode('validation_groups')->treatNullLike(['Resetting', 'Default'])->scalarPrototype()->end()->end()
                        ->scalarNode('template')->defaultValue('@SfsUser/resetting/resetting.html.twig')->end()

                        ->arrayNode('email')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('from')
                                    ->children()
                                        ->scalarNode('address')->end()
                                        ->scalarNode('name')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('success')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('route')->defaultValue('sfs_user_resetting_success')->end()
                                ->arrayNode('route_params')->treatNullLike([])->scalarPrototype()->end()->end()
                                ->scalarNode('template')->defaultValue('@SfsUser/resetting/success.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('change_email')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('form')->defaultValue(ChangeEmailForm::class)->end()
                        ->arrayNode('validation_groups')->treatNullLike(['ChangeEmail', 'Default'])->scalarPrototype()->end()->end()
                        ->scalarNode('template')->defaultValue('@SfsUser/change_email/change_email.html.twig')->end()
                        ->arrayNode('success')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('route')->defaultValue('sfs_user_change_email_success')->end()
                                ->arrayNode('route_params')->treatNullLike([])->scalarPrototype()->end()->end()
                                ->scalarNode('template')->defaultValue('@SfsUser/change_email/success.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('change_username')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('form')->defaultValue(ChangeUsernameForm::class)->end()
                        ->arrayNode('validation_groups')->treatNullLike(['ChangeUsername', 'Default'])->scalarPrototype()->end()->end()
                        ->scalarNode('template')->defaultValue('@SfsUser/change_username/change_username.html.twig')->end()
                        ->arrayNode('success')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('route')->defaultValue('sfs_user_change_username_success')->end()
                                ->arrayNode('route_params')->treatNullLike([])->scalarPrototype()->end()->end()
                                ->scalarNode('template')->defaultValue('@SfsUser/change_username/success.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('impersonate_bar')
                    ->canBeEnabled()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('switch_role')->defaultValue('ROLE_ALLOWED_TO_SWITCH')->end()
                        ->scalarNode('switch_route')->defaultValue('configure_switch_route')->end()
                        ->arrayNode('switch_route_params')->treatNullLike([])->scalarPrototype()->end()->end()
                        ->scalarNode('switch_parameter')->defaultValue('_switch_user')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}