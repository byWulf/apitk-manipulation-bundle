<?php

declare(strict_types=1);

namespace Shopping\ApiTKUpdateBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @example Payload("deal", type=DealV1Type::class)
 * @example Payload("user", type=UserV1Type::class, entityManager="otherConnection")
 * @example Payload(
 *      "item",
 *      type="App\Form\Type\ItemV1Type",
 *      requestParam="item_name",
 *      repositoryFindMethodName="findByName"
 * )
 *
 * @package Shopping\ApiTKUpdateBundle\Annotation
 *
 * @Annotation
 */
class Payload extends ParamConverter
{
    /**
     * Specify the name of this filter.
     *
     * @var string
     */
    public $name;

    /**
     * @param $type
     */
    public function setType($type)
    {
        $options = $this->getOptions();
        $options['type'] = $type;

        $this->setOptions($options);
    }

    /**
     * @param $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $options = $this->getOptions();
        $options['entityManager'] = $entityManager;

        $this->setOptions($options);
    }

    /**
     * @param $repositoryFindMethodName
     */
    public function setRepositoryFindMethodName($repositoryFindMethodName)
    {
        $options = $this->getOptions();
        $options['repositoryFindMethodName'] = $repositoryFindMethodName;

        $this->setOptions($options);
    }

    /**
     * @param $requestParam
     */
    public function setRequestParam($requestParam)
    {
        $options = $this->getOptions();
        $options['requestParam'] = $requestParam;

        $this->setOptions($options);
    }
}
