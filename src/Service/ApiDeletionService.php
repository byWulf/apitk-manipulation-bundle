<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Service;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiDeletionService
{
    private string $parameterName = '';

    private string $parameterValue = '';

    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    public function setParameterName(string $parameterName): ApiDeletionService
    {
        $this->parameterName = $parameterName;

        return $this;
    }

    public function getParameterValue(): string
    {
        return $this->parameterValue;
    }

    public function setParameterValue(string $parameterValue): ApiDeletionService
    {
        $this->parameterValue = $parameterValue;

        return $this;
    }

    public function getRequestStack(): RequestStack
    {
        return $this->requestStack;
    }

    /**
     * Default handling for entity deletion when no repository method has been supplied.
     * Will be called by DeleteConverter.
     *
     * @param ObjectRepository<mixed> $repository
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function deleteEntity(ObjectManager $manager, ObjectRepository $repository, string $primaryKey): bool
    {
        $entity = $repository->find($primaryKey);

        if ($entity === null) {
            throw new EntityNotFoundException();
        }

        $manager->remove($entity);
        $manager->flush();

        return true;
    }
}
