<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Application\SearchByCriteria;

use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleRepository;
use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filters;
use Dba\DddSkeleton\Shared\Domain\Criteria\Order;

final class ArticlesByCriteriaCounter
{
    public function __construct(private readonly ArticleRepository $repository) {}

    public function __invoke(Filters $filters, Order $order, ?int $limit, ?int $offset): int
    {
        $criteria = new Criteria($filters, $order, $offset, $limit);

        return $this->repository->countByCriteria($criteria);
    }
}
