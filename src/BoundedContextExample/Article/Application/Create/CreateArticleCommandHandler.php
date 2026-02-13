<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Application\Create;

use Dba\DddSkeleton\BoundedContextExample\Article\Domain\Article;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleId;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleName;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleRepository;

final class CreateArticleCommandHandler
{
    public function __construct(private readonly ArticleRepository $repository) {}

    public function __invoke(CreateArticleCommand $command): void
    {
        $id = new ArticleId($command->id());
        $name = new ArticleName($command->name());

        $article = Article::create($id, $name, $command->price(), $command->stock());

        $this->repository->save($article);
    }
}
