<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\ParamConverter;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Shopping\ApiTKCommonBundle\ParamConverter\ContextAwareParamConverterTrait;
use Shopping\ApiTKCommonBundle\ParamConverter\EntityAwareParamConverterTrait;
use Shopping\ApiTKManipulationBundle\Annotation\Delete;
use Shopping\ApiTKManipulationBundle\Exception\DeletionException;
use Shopping\ApiTKManipulationBundle\Service\ApiDeletionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ParamConverter to delete an entity from the database.
 */
class DeleteConverter implements ParamConverterInterface
{
    use ContextAwareParamConverterTrait;
    use EntityAwareParamConverterTrait;

    public function __construct(
        private ApiDeletionService $deletionService,
        ManagerRegistry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @throws EntityNotFoundException
     * @throws DeletionException
     * @throws InvalidArgumentException
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $this->initialize($request, $configuration);

        if ($this->getEntity() === null) {
            throw new InvalidArgumentException('You have to specify "entity" option for the DeleteConverter.');
        }

        $requestParamName = $configuration->getName();
        $requestParam = $request->attributes->get($requestParamName);

        $this->deleteInRepository($requestParam, $requestParamName);

        $response = new Response(null, Response::HTTP_NO_CONTENT);
        $request->attributes->set('response', $response);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration): bool
    {
        return $configuration instanceof Delete && $this->registry instanceof ManagerRegistry;
    }

    /**
     * Essentially a wrapper around self::performDeletion() that normalizes its possible exceptions.
     *
     * @throws DeletionException
     * @throws EntityNotFoundException
     *
     * @return bool Deletion status
     */
    private function deleteInRepository(string $requestParam, string $requestParamName): bool
    {
        /*
         * normalize exceptions:
         * - DeleteException for ORM errors
         * - EntityNotFoundException when the given ID doesn't return an entity
         */
        try {
            $result = $this->performDeletion($requestParam, $requestParamName);
        } catch (EntityNotFoundException $e) {
            // check for meaningful error message and re-raise
            if (empty($e->getMessage())) {
                throw new EntityNotFoundException(
                    sprintf(
                        'Unable to find Entity of class %s with %s "%s" for deletion.',
                        $this->getEntity(),
                        $requestParamName,
                        $requestParam
                    ),
                    0,
                    $e
                );
            }

            throw $e;
        } catch (OptimisticLockException|ORMException $e) {
            throw new DeletionException($e->getMessage(), 0, $e);
        }

        // deleteByRequest can return false when the deletion procedure failed or 0 rows were deleted, etc.
        if ($result === false) {
            throw new DeletionException(
                sprintf(
                    'Unable to delete Entity of class %s with %s "%s".',
                    $this->getEntity(),
                    $requestParamName,
                    $requestParam
                )
            );
        }

        return $result;
    }

    /**
     * Either call a custom repository method to delete a given entity based on all request params or perform
     * the default off-the-shelf deletion where the entity will be removed based on its primary key.
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function performDeletion(string $requestParam, string $requestParamName): bool
    {
        $om = $this->getManager();
        if ($om === null) {
            throw new RuntimeException('Unable to perform deletion as EntityManager is null');
        }

        /** @var class-string $entityClassName */
        $entityClassName = $this->getEntity();

        $repository = $om->getRepository($entityClassName);
        $methodName = $this->getRepositoryMethodName();

        if ($methodName === null) {
            return $this->deletionService->deleteEntity($om, $repository, $requestParam);
        }

        // fill deletion service with appropriate values
        $this->deletionService
            ->setParameterName($requestParamName)
            ->setParameterValue($requestParam);

        return $this->callRepositoryMethod($methodName, $this->deletionService);
    }
}
