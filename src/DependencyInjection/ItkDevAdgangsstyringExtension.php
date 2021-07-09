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
            'tenant_id' => $config['adgangsstyring_options']['tenant_id'],
            'client_id' => $config['adgangsstyring_options']['client_id'],
            'client_secret' => $config['adgangsstyring_options']['client_secret'],
            'group_id' => $config['adgangsstyring_options']['group_id'],
        ];

        $definition = $container->getDefinition(AccessControlCommand::class);
        $definition->replaceArgument('$options', $options);
        $definition->replaceArgument('$userClass', $config['user_options']['user_class']);
        $definition->replaceArgument('$username', $config['user_options']['username']);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'itkdev_adgangsstyring';
    }
}
