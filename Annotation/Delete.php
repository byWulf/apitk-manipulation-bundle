<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Shopping\ApiTKCommonBundle\Annotation\ParamConverter\EntityAwareAnnotationTrait;

/**
 * Class Delete.
 *
 * @Annotation
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
 *
 * @package Shopping\ApiTKManipulationBundle\Annotation
 */
class Delete extends ParamConverter
{
    use EntityAwareAnnotationTrait;
}
