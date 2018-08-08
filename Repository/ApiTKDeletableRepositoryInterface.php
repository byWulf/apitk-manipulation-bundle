<?php

declare(strict_types=1);

namespace Shopping\ApiTKUpdateBundle\Repository;

/**
 * Interface ApiTKDeletableRepositoryInterface.
 *
 * Repositories need to implement this interface to use the @Update\Delete annotation.
 *
 * @package Shopping\ApiTKUpdateBundle\Repository
 *
 * @author Alexander Dormann <alexander.dormann@check24.de>
 */
interface ApiTKDeletableRepositoryInterface
{
    /**
     * Delete the entity for a given parameter.
     * You may delete the entity from the database here, perform a soft delete
     * or other things to mark your entity as deleted.
     *
     * @param string $idParameter
     *
     * The param converter is able to handle these exceptions, so use them appropriately:
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\EntityNotFoundException
     *
     * @return bool deletion status
     */
    public function deleteByRequest(string $idParameter): bool;
}
