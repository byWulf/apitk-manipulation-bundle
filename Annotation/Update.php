<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Shopping\ApiTKCommonBundle\Annotation\ParamConverter\EntityAwareAnnotationTrait;
use Shopping\ApiTKCommonBundle\Annotation\ParamConverter\RequestParamAwareAnnotationTrait;

/**
 * Class Update.
 *
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
 *
 * @package Shopping\ApiTKManipulationBundle\Annotation
 */
class Update extends ParamConverter
{
    use EntityAwareAnnotationTrait;
    use RequestParamAwareAnnotationTrait;

    /**
     * Specify the name of this filter.
     *
     * @var string
     */
    public $name;

    /**
     * @param string $type
     */
    public function setType($type): void
    {
        $options = $this->getOptions();
        $options['type'] = $type;

        $this->setOptions($options);
    }
}
