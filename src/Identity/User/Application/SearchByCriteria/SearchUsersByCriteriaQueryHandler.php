<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\SearchByCriteria;

use Dba\DddSkeleton\Identity\User\Application\Response\UserResponse;
use Dba\DddSkeleton\Identity\User\Application\Response\UsersResponse;
use Dba\DddSkeleton\Identity\User\Domain\User;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\QueryHandler;
use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filters;
use Dba\DddSkeleton\Shared\Domain\Criteria\Order;

final readonly class SearchUsersByCriteriaQueryHandler implements QueryHandler
{
    public function __construct(private UserRepository $repository) {}

    public function __invoke(SearchUsersByCriteriaQuery $query): UsersResponse
    {
        $filters = Filters::fromValues(['groups' => $query->filters()]);
        $order   = Order::fromValues($query->orderBy(), $query->orderType());

        $criteria = new Criteria($filters, $order, $query->offset(), $query->limit());

        $users = $this->repository->searchByCriteria($criteria);

        return new UsersResponse(
            array_map(fn (User $user) => UserResponse::fromAggregate($user), $users)
        );
    }
}
