<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Application\SearchByCriteria;

use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleRepository;
use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filters;
use Dba\DddSkeleton\Shared\Domain\Criteria\Order;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\CountResponse;

final class CountArticlesByCriteriaQueryHandler
{
    public function __construct(private readonly ArticleRepository $repository) {}

    public function __invoke(CountArticlesByCriteriaQuery $query): CountResponse
    {
        $filters = Filters::fromValues($query->filters());
        $order = Order::fromValues($query->orderBy(), $query->orderType());

        $criteria = new Criteria(
            $filters,
            $order,
            $query->offset(),
            $query->limit()
        );

        return new CountResponse($this->repository->countByCriteria($criteria));
    }
}
