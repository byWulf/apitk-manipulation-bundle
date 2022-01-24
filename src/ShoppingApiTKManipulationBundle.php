<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle;

use Shopping\ApiTKManipulationBundle\DependencyInjection\ShoppingApiManipulationBundleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShoppingApiTKManipulationBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new ShoppingApiManipulationBundleExtension();
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
