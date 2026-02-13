<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Infrastructure\Controller;

use Dba\DddSkeleton\BoundedContextExample\Article\Application\Find\FindArticleQuery;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\Find\FindArticleQueryHandler;
use Illuminate\Http\JsonResponse;

final class FindArticleController
{
    public function __construct(private readonly FindArticleQueryHandler $handler) {}

    public function __invoke(string $id): JsonResponse
    {
        /** @var \Dba\DddSkeleton\BoundedContextExample\Article\Application\Response\ArticleResponse|null $response */
        $response = ($this->handler)(new FindArticleQuery($id));

        if (null === $response) {
            return new JsonResponse(['error' => 'Not Found'], 404);
        }

        return new JsonResponse($response->toArray(), 200);
    }
}
