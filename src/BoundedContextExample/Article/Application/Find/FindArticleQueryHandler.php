<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Application\Find;

use Dba\DddSkeleton\BoundedContextExample\Article\Application\Response\ArticleResponse;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\Article;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleId;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleRepository;

final class FindArticleQueryHandler
{
    public function __construct(private readonly ArticleRepository $repository) {}

    public function __invoke(FindArticleQuery $query): ?ArticleResponse
    {
        $id = new ArticleId($query->id());
        $article = $this->repository->search($id);

        return $article ? ArticleResponse::fromAggregate($article) : null;
    }
}
