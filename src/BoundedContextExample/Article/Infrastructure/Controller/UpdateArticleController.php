<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Infrastructure\Controller;

use Dba\DddSkeleton\BoundedContextExample\Article\Application\Update\UpdateArticleCommand;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\Update\UpdateArticleCommandHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UpdateArticleController
{
    public function __construct(private readonly UpdateArticleCommandHandler $handler) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $command = new UpdateArticleCommand(
            $id,
            $request->input('name'),
            $request->input('price') ? (float) $request->input('price') : null,
            $request->input('stock') ? (int) $request->input('stock') : null
        );

        ($this->handler)($command);

        return new JsonResponse(null, 200);
    }
}
