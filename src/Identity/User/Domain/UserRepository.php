<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Domain;

use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;

interface UserRepository
{
    /**
     * Persists a User aggregate.
     */
    public function save(User $user): void;

    /**
     * Removes a user by their unique identifier.
     */
    public function remove(UserId $id): void;

    /**
     * Finds a user by their ID. Returns null if not found.
     */
    public function search(UserId $id): ?User;

    /**
     * Returns an array of User entities matching the given criteria.
     * Uses the Shared Criteria pattern for filtering and pagination.
     */
    public function searchByCriteria(Criteria $criteria): array;

    /**
     * Returns the total count of users matching the given criteria.
     */
    public function countByCriteria(Criteria $criteria): int;
}