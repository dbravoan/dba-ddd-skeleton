<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Domain;

use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;

interface UserRepository
{
    public function save(User $user): void;

    public function search(UserId $id): ?User;

    /** @return array<int, User> */
    public function searchByCriteria(Criteria $criteria): array;

    public function delete(UserId $id): bool;
}
