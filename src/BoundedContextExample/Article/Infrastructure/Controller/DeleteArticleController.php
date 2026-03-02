<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Infrastructure\Controller;

use Dba\DddSkeleton\BoundedContextExample\Article\Application\Delete\DeleteArticleCommand;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\Delete\DeleteArticleCommandHandler;
use Dba\DddSkeleton\Shared\Infrastructure\Laravel\ApiController;
use Illuminate\Http\JsonResponse;

final class DeleteArticleController extends ApiController
{
    public function __construct(private readonly DeleteArticleCommandHandler $handler) {}

    public function __invoke(string $id): JsonResponse
    {
        ($this->handler)(new DeleteArticleCommand($id));

        return $this->sendResponse(null, 'Article deleted successfully');
    }
}
