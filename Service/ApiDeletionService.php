<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ApiDeletionService.
 *
 * @package Shopping\ApiTKManipulationBundle\Service
 *
 * @author Alexander Dormann <alexander.dormann@check24.de>
 */
class ApiDeletionService
{
    /**
     * @var string
     */
    private $parameterName;

    /**
     * @var string
     */
    private $parameterValue;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ApiDeletionService constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return string
     */
    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    /**
     * @param string $parameterName
     *
     * @return ApiDeletionService
     */
    public function setParameterName(string $parameterName): ApiDeletionService
    {
        $this->parameterName = $parameterName;

        return $this;
    }

    /**
     * @return string
     */
    public function getParameterValue(): string
    {
        return $this->parameterValue;
    }

    /**
     * @param string $parameterValue
     *
     * @return ApiDeletionService
     */
    public function setParameterValue(string $parameterValue): ApiDeletionService
    {
        $this->parameterValue = $parameterValue;

        return $this;
    }

    /**
     * @return RequestStack
     */
    public function getRequestStack(): RequestStack
    {
        return $this->requestStack;
    }
}
