<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Find;

use Dba\DddSkeleton\Identity\User\Application\Response\UserResponse;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\QueryHandler;

final readonly class FindUserQueryHandler implements QueryHandler
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function __invoke(FindUserQuery $query): ?UserResponse
    {
        $user = $this->repository->search(new UserId($query->id));

        return $user ? UserResponse::fromAggregate($user) : null;
    }
}