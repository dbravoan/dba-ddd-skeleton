<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Infrastructure\Controller;

use Dba\DddSkeleton\BoundedContextExample\Article\Application\SearchByCriteria\CountArticlesByCriteriaQueryHandler;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\SearchByCriteria\SearchArticlesByCriteriaQuery;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\SearchByCriteria\SearchArticlesByCriteriaQueryHandler;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\SearchByCriteria\CountArticlesByCriteriaQuery;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filters;
use Dba\DddSkeleton\Shared\Domain\Criteria\Order;
use Dba\DddSkeleton\Shared\Infrastructure\Criteria\RequestCriteriaBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SearchArticlesByCriteriaController
{
    public function __construct(
        private readonly RequestCriteriaBuilder $requestCriteriaBuilder,
        private readonly SearchArticlesByCriteriaQueryHandler $searcher,
        private readonly CountArticlesByCriteriaQueryHandler $counter
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $criteria = $this->requestCriteriaBuilder->buildFromRequest($request);
        $offset = (int) $criteria->offset();
        $limit = $criteria->limit();

        $searchQuery = new SearchArticlesByCriteriaQuery(
            $criteria->filters(),
            $criteria->order(),
            $limit,
            $offset
        );

        /** @var ArticlesResponse $articlesResponse */
        $articlesResponse = ($this->searcher)($searchQuery);

        $filteredRecords = ($this->counter)(new CountArticlesByCriteriaQuery(
            $criteria->filters(),
            Order::none(),
            null,
            null
        ));

        $totalRecords = ($this->counter)(new CountArticlesByCriteriaQuery(
            new Filters([]),
            Order::none(),
            null,
            null
        ));

        $currentPage = $limit ? (int) floor($offset / $limit) + 1 : 1;
        $totalPages = $limit ? (int) ceil($filteredRecords / $limit) : 1;

        return new JsonResponse([
            'records' => $articlesResponse->toArray(),
            'meta' => [
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'filtered_records' => $filteredRecords,
                'total_records' => $totalRecords,
                'per_page' => $limit
            ]
        ], 200);
    }
}
