<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Infrastructure\Controller;

use Dba\DddSkeleton\BoundedContextExample\Article\Application\Create\CreateArticleCommand;
use Dba\DddSkeleton\BoundedContextExample\Article\Application\Create\CreateArticleCommandHandler;
use Dba\DddSkeleton\Shared\Infrastructure\Laravel\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CreateArticleController extends ApiController
{
    public function __construct(private readonly CreateArticleCommandHandler $handler) {}

    public function __invoke(Request $request): JsonResponse
    {
        $command = new CreateArticleCommand(
            $request->input('id'),
            $request->input('name'),
            (float) $request->input('price'),
            (int) $request->input('stock')
        );

        ($this->handler)($command);

        return $this->sendResponse(null, 'Article created successfully');
    }
}
