<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Laravel\Providers;

use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Repository bindings: DomainInterface => EloquentImplementation.
     *
     * Override this provider in your application to add your own bindings.
     *
     * @see README.md section "Service Providers"
     * @var array<class-string, class-string>
     */
    private array $repositories = [
        // \YourContext\Module\Domain\ModuleRepository::class => \YourContext\Module\Infrastructure\Persistence\EloquentModuleRepository::class,
    ];

    public function register(): void
    {
        foreach ($this->repositories as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }
}
