<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Shared\Infrastructure\Laravel\Providers;

use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleRepository;
use Dba\DddSkeleton\BoundedContextExample\Article\Infrastructure\Persistence\EloquentArticleRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->bindRepositories();
    }

    private function bindRepositories(): void
    {
        // Article Module
        $this->app->bind(ArticleRepository::class, EloquentArticleRepository::class);

        // Add other module bindings here
    }
}
