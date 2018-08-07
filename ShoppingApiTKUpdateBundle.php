<?php

declare(strict_types=1);

namespace Shopping\ApiTKUpdateBundle;

use Shopping\ApiTKUpdateBundle\DependencyInjection\ShoppingApiTKUpdateBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ShoppingApiTKUpdateBundle.
 *
 * @package Shopping\ApiTKUpdateBundle
 *
 * @author Alexander Dormann <alexander.dormann@check24.de>
 */
class ShoppingApiTKUpdateBundle extends Bundle
{
    /**
     * @return ShoppingApiTKUpdateBundleExtension
     */
    public function getContainerExtension()
    {
        return new ShoppingApiTKUpdateBundleExtension();
    }
}
