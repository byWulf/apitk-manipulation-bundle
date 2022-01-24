<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Shopping\ApiTKCommonBundle\Annotation\ParamConverter\EntityAwareAnnotationTrait;
use Shopping\ApiTKCommonBundle\Annotation\ParamConverter\RequestParamAwareAnnotationTrait;

/**
 * @Annotation
 *
 * Annotation for automatic handling of POST, PUT and PATCH methods.
 *
 * @example Update("deal", type=DealV1Type::class)
 * @example Update("user", type=UserV1Type::class, entityManager="otherConnection")
 * @example Update(
 *      "item",
 *      type="App\Form\Type\ItemV1Type",
 *      requestParam="item_name",
 *      repositoryFindMethodName="findByName"
 * )
 */
class Update extends ParamConverter
{
    use EntityAwareAnnotationTrait;
    use RequestParamAwareAnnotationTrait;

    public function setType(string $type): void
    {
        $options = $this->getOptions();
        $options['type'] = $type;

        $this->setOptions($options);
    }
}
