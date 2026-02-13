<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Application\Delete;

use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleId;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleRepository;

final class DeleteArticleCommandHandler
{
    public function __construct(private readonly ArticleRepository $repository) {}

    public function __invoke(DeleteArticleCommand $command): void
    {
        $this->repository->delete(new ArticleId($command->id()));
    }
}
