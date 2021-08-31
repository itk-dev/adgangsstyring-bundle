<?php

namespace ItkDev\AzureAdDeltaSyncBundle\DependencyInjection;

use ItkDev\AzureAdDeltaSync\Controller;
use ItkDev\AzureAdDeltaSyncBundle\Command\AccessControlCommand;
use ItkDev\AzureAdDeltaSyncBundle\Handler\UserHandler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class ItkDevAzureAdDeltaSyncExtension extends Extension
{

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $options = [
            'tenant_id' => $config['azure_ad_delta_sync_options']['tenant_id'],
            'client_id' => $config['azure_ad_delta_sync_options']['client_id'],
            'client_secret' => $config['azure_ad_delta_sync_options']['client_secret'],
            'group_id' => $config['azure_ad_delta_sync_options']['group_id'],
        ];

        $definition = $container->getDefinition(Controller::class);
        $definition->replaceArgument('$options', $options);

        $definition = $container->getDefinition(UserHandler::class);
        $definition->replaceArgument('$cache', new Reference($config['cache_options']['cache_pool']));
        $definition->replaceArgument('$user_class', $config['user_options']['system_user_class']);
        $definition->replaceArgument('$user_property', $config['user_options']['system_user_property']);
        $definition->replaceArgument('$user_claim_property', $config['user_options']['azure_ad_user_property']);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'itkdev_azure_ad_delta_sync';
    }
}
