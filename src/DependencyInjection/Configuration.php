<?php

namespace ItkDev\AdgangsstyringBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('itkdev_adgangsstyring');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('adgangsstyring_options')
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
                ->scalarNode('user_class')
                    ->info('User class name')
                    ->cannotBeEmpty()->end()
               ->scalarNode('username')
                    ->info('Unique username')
                    ->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }
}
