<?php

declare(strict_types=1);

namespace Dba\DddSkeleton;

use Dba\DddSkeleton\Console\Commands\MakeModuleCommand;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandBus;
use Dba\DddSkeleton\Shared\Domain\Bus\Event\EventBus;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\QueryBus;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\Command\LaravelCommandBus;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\Event\Laravel\LaravelEventBus;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\Query\LaravelQueryBus;
use Dba\DddSkeleton\Shared\Infrastructure\Laravel\Providers\RepositoryServiceProvider;
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
                __DIR__.'/Console/Commands/Stubs' => resource_path('stubs/dbravoan/dba-ddd-skeleton'),
            ], 'dba-ddd-skeleton-stubs');
        }
    }

    public function register(): void
    {
        // Register the Example Repository Provider
        $this->app->register(RepositoryServiceProvider::class);

        $this->registerBuses();
    }

    private function registerBuses(): void
    {
        $this->app->singleton(CommandBus::class, function ($app) {
            return new LaravelCommandBus(
                $app->tagged('dba_ddd.command_handler')
            );
        });

        $this->app->singleton(QueryBus::class, function ($app) {
            return new LaravelQueryBus(
                $app->tagged('dba_ddd.query_handler')
            );
        });

        $this->app->singleton(LaravelEventBus::class, function ($app) {
            return new LaravelEventBus(
                $app->tagged('dba_ddd.domain_event_subscriber')
            );
        });

        // By default, EventBus resolves to the sync implementation.
        // Override this binding with LaravelQueueEventBus for async processing.
        $this->app->singleton(EventBus::class, LaravelEventBus::class);
    }
}
