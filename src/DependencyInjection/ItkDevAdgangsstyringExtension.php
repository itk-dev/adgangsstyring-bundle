<?php

namespace ItkDev\AdgangsstyringBundle\DependencyInjection;

use ItkDev\AdgangsstyringBundle\Command\AccessControlCommand;
use ItkDev\AdgangsstyringBundle\EventSubscriber\EventSubscriber;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\RegisterServiceSubscribersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

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
            'tenantId' => $config['adgangsstyring_options']['tenant_id'],
            'clientId' => $config['adgangsstyring_options']['client_id'],
            'clientSecret' => $config['adgangsstyring_options']['client_secret'],
            'groupId' => $config['adgangsstyring_options']['group_id'],
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
