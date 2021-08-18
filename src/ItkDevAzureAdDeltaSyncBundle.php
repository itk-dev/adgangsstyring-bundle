<?php

namespace ItkDev\AzureAdDeltaSyncBundle;

use ItkDev\AzureAdDeltaSyncBundle\DependencyInjection\ItkDevAzureAdDeltaSyncExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ItkDevAzureAdDeltaSyncBundle extends Bundle
{
    /**
     * {@inheritdoc}
     *
     * Overridden to allow for the custom extension alias.
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ItkDevAzureAdDeltaSyncExtension();
        }

        return $this->extension;
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
