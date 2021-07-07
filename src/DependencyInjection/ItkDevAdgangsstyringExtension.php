<?php

namespace ItkDev\AdgangsstyringBundle\DependencyInjection;

use ItkDev\AdgangsstyringBundle\Command\AccessControlCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ItkDevAdgangsstyringExtension extends Extension
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
            'tenantId' => $config['tenant_id'],
            'clientId' => $config['client_id'],
            'clientSecret' => $config['client_secret'],
            'groupId' => $config['group_id'],
        ];

        $definition = $container->getDefinition(AccessControlCommand::class);
        $definition->replaceArgument('$options', $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'itkdev_adgangsstyring';
    }
}
