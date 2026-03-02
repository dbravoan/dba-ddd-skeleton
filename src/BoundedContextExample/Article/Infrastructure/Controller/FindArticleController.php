<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Infrastructure\Controller;

use Dba\DddSkeleton\BoundedContextExample\Article\Application\Find\FindArticleQuery;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\Find\FindArticleQueryHandler;
use Dba\DddSkeleton\Shared\Infrastructure\Laravel\ApiController;
use Illuminate\Http\JsonResponse;

final class FindArticleController extends ApiController
{
    public function __construct(private readonly FindArticleQueryHandler $handler) {}

    public function __invoke(string $id): JsonResponse
    {
        /** @var \Dba\DddSkeleton\BoundedContextExample\Article\Application\Response\ArticleResponse|null $response */
        $response = ($this->handler)(new FindArticleQuery($id));

        if (null === $response) {
            return $this->sendError('Not Found', [], 404);
        }

        return $this->sendResponse($response->toArray(), 'Article found successfully');
    }
}
