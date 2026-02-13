<?php

declare(strict_types=1);

namespace Dba\DddSkeleton;

use Dba\DddSkeleton\Console\Commands\MakeModuleCommand;
use Illuminate\Support\ServiceProvider;

final class DddSkeletonServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModuleCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/Console/Commands/Stubs' => resource_path('stubs/dbravoan/dba-ddd-skeleton'),
            ], 'dba-ddd-skeleton-stubs');
        }
    }

    public function register(): void
    {
        // Register the Example Repository Provider (Optional, for demonstration)
        // In a real app, the user would register their own.
        $this->app->register(\Dba\DddSkeleton\BoundedContextExample\Shared\Infrastructure\Laravel\Providers\RepositoryServiceProvider::class);
    }
}
