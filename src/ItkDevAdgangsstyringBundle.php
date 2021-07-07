<?php

namespace ItkDev\AdgangsstyringBundle;

use ItkDev\AdgangsstyringBundle\DependencyInjection\ItkDevAdgangsstyringExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ItkDevAdgangsstyringBundle extends Bundle
{
    /**
     * {@inheritdoc}
     *
     * Overridden to allow for the custom extension alias.
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ItkDevAdgangsstyringExtension();
        }

        return $this->extension;
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
