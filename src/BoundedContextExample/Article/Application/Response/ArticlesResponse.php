<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Application\Response;

final class ArticlesResponse
{
    private array $articles;

    public function __construct(ArticleResponse ...$articles)
    {
        $this->articles = $articles;
    }

    public function articles(): array
    {
        return $this->articles;
    }

    public function toArray(): array
    {
        return array_map(fn(ArticleResponse $response) => $response->toArray(), $this->articles);
    }
}
