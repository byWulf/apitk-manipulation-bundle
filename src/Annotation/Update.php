<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Shopping\ApiTKCommonBundle\Annotation\ParamConverter\EntityAwareAnnotationTrait;
use Shopping\ApiTKCommonBundle\Annotation\ParamConverter\RequestParamAwareAnnotationTrait;
use Attribute;

/**
 * @Annotation
 * @NamedArgumentConstructor()
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
#[Attribute(Attribute::TARGET_METHOD)]
class Update extends ParamConverter
{
    use EntityAwareAnnotationTrait;
    use RequestParamAwareAnnotationTrait;

    public function __construct(
        string $name,
        string $type,
        ?string $entityManager = null,
        ?string $requestParam = null,
        ?string $repositoryFindMethodName = null,
    ) {
        $options = [
            'type' => $type,
        ];

        if ($entityManager !== null) {
            $options['entityManager'] = $entityManager;
        }

        if ($requestParam !== null) {
            $options['requestParam'] = $requestParam;
        }

        if ($repositoryFindMethodName !== null) {
            $options['methodName'] = $repositoryFindMethodName;
        }

        parent::__construct($name, null, $options);
    }

    public function setType(string $type): void
    {
        $options = $this->getOptions();
        $options['type'] = $type;

        $this->setOptions($options);
    }
}
