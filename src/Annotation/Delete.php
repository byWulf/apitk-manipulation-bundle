<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Shopping\ApiTKCommonBundle\Annotation\ParamConverter\EntityAwareAnnotationTrait;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 *
 * Annotation for automatic handling of DELETE methods.
 * Use your primary key path component as first argument and the corresponding entity class as entity option.
 *
 * For a given path "/api/v1/user/{id}", the correct annotation is Delete("id", entity=User::class). It will extract
 * the path component from the url and pass it as parameter to your UserRepository.
 *
 * @example Delete("id", entity=User::class)
 * @example Delete("email", entity=User::class)
 * @example Delete("itemId", entity=Item::class, entityManager="someOtherConnection")
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Delete extends ParamConverter
{
    use EntityAwareAnnotationTrait;

    public function __construct(
        string $name,
        string $entity,
        ?string $entityManager = null,
        ?string $repositoryFindMethodName = null
    ) {
        $options = [
            'entity' => $entity,
        ];

        if ($entityManager !== null) {
            $options['entityManager'] = $entityManager;
        }

        if ($repositoryFindMethodName !== null) {
            $options['methodName'] = $repositoryFindMethodName;
        }

        parent::__construct($name, null, $options);
    }
}
