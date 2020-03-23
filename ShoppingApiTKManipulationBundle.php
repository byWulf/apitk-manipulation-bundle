<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle;

use Shopping\ApiTKManipulationBundle\DependencyInjection\ShoppingApiManipulationBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ShoppingApiTKManipulationBundle.
 *
 * @package Shopping\ApiTKManipulationBundle
 */
class ShoppingApiTKManipulationBundle extends Bundle
{
    /**
     * @return ShoppingApiManipulationBundleExtension
     */
    public function getContainerExtension()
    {
        return new ShoppingApiManipulationBundleExtension();
    }
}
