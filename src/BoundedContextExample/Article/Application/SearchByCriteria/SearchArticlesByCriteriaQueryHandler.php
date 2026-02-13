<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Application\SearchByCriteria;

use Dba\DddSkeleton\BoundedContextExample\Article\Application\Response\ArticleResponse;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\Response\ArticlesResponse;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\Article;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filters;
use Dba\DddSkeleton\Shared\Domain\Criteria\Order;

final class SearchArticlesByCriteriaQueryHandler
{
    public function __construct(private readonly ArticlesByCriteriaSearcher $searcher) {}

    public function __invoke(SearchArticlesByCriteriaQuery $query): ArticlesResponse
    {
        $filters = new Filters($query->filters());
        $order = Order::fromValues($query->orderBy(), $query->orderType());

        $entities = $this->searcher->search(
            $filters,
            $order,
            $query->limit(),
            $query->offset()
        );

        $responses = array_map(
            fn(Article $article) => ArticleResponse::fromAggregate($article),
            $entities
        );

        return new ArticlesResponse(...$responses);
    }
}
