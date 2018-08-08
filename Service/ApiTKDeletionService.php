<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ApiTKDeletionService.
 *
 * @package Shopping\ApiTKManipulationBundle\Service
 *
 * @author Alexander Dormann <alexander.dormann@check24.de>
 */
class ApiTKDeletionService
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
     * ApiTKDeletionService constructor.
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
     * @return ApiTKDeletionService
     */
    public function setParameterName(string $parameterName): ApiTKDeletionService
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
     * @return ApiTKDeletionService
     */
    public function setParameterValue(string $parameterValue): ApiTKDeletionService
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
