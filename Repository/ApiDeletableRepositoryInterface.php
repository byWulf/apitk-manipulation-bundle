<?php

declare(strict_types=1);

namespace Shopping\ApiTKManipulationBundle\Repository;

use Shopping\ApiTKManipulationBundle\Service\ApiDeletionService;

/**
 * Interface ApiTKDeletableRepositoryInterface.
 *
 * Repositories need to implement this interface to use the @Manipulation\Delete annotation.
 *
 * @package Shopping\ApiTKManipulationBundle\Repository
 *
 * @author Alexander Dormann <alexander.dormann@check24.de>
 */
interface ApiDeletableRepositoryInterface
{
    /**
     * Delete the entity for a given parameter.
     * You may delete the entity from the database here, perform a soft delete
     * or other things to mark your entity as deleted.
     *
     * @param ApiDeletionService $deletionService
     *
     * The param converter is able to handle these exceptions, so use them appropriately:
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\EntityNotFoundException
     *
     * @return bool deletion status
     */
    public function deleteByRequest(ApiDeletionService $deletionService): bool;
}
