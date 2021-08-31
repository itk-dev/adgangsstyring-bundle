<?php

namespace ItkDev\AzureAdDeltaSyncBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('itkdev_azure_ad_delta_sync');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('azure_ad_delta_sync_options')
                ->isRequired()
                    ->children()
                        ->scalarNode('tenant_id')
                            ->info('Tenant ID provided by authorizer')
                            ->cannotBeEmpty()->end()
                        ->scalarNode('client_id')
                            ->info('Client ID provided by authorizer')
                            ->cannotBeEmpty()->end()
                        ->scalarNode('client_secret')
                            ->info('Client secret/password provided by authorizer')
                            ->cannotBeEmpty()->end()
                        ->scalarNode('group_id')
                            ->info('Group ID provided by authorizer')
                            ->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('user_options')
                ->isRequired()
                    ->children()
                        ->scalarNode('system_user_class')
                            ->info('The User class name.')
                            ->cannotBeEmpty()->end()
                        ->scalarNode('system_user_property')
                            ->info('Unique user property.')
                            ->cannotBeEmpty()->end()
                        ->scalarNode('azure_ad_user_property')
                            ->info('Azure AD user property.')
                            ->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('cache_options')
                ->isRequired()
                    ->children()
                        ->scalarNode('cache_pool')
                            ->info('Method for caching')
                            ->defaultValue('cache.app')
                            ->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
