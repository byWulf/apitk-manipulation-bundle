<?php

declare(strict_types=1);

namespace Shopping\ApiTKUpdateBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
 * @package Shopping\ApiTKUpdateBundle\Annotation
 *
 * @author Alexander Dormann <alexander.dormann@check24.de>
 */
class Delete extends ParamConverter
{
    /**
     * @param $entityName
     */
    public function setEntity($entityName)
    {
        $options = $this->getOptions();
        $options['entity'] = $entityName;

        $this->setOptions($options);
    }

    /**
     * @param $manager
     */
    public function setEntityManager($manager)
    {
        $options = $this->getOptions();
        $options['entityManager'] = $manager;

        $this->setOptions($options);
    }
}
