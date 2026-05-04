<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Laravel\Providers;

use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandHandler;
use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEventSubscriber;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\QueryHandler;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

abstract class DbaServiceProvider extends ServiceProvider
{
    abstract protected function contextName(): string;

    abstract protected function moduleName(): string;

    public function register(): void
    {
        $this->registerHandlers();
    }

    protected function registerHandlers(): void
    {
        $context = $this->contextName();
        $module = $this->moduleName();
        $path = base_path("src/{$context}/{$module}/Application");

        if (! is_dir($path)) {
            return;
        }

        $finder = new Finder;
        $finder->files()->in($path)->name('*Handler.php');

        foreach ($finder as $file) {
            $class = $this->getClassFromFile($file->getRealPath());

            if ($class && class_exists($class)) {
                if (is_a($class, CommandHandler::class, true)) {
                    $this->app->tag($class, 'dba_ddd.command_handler');
                } elseif (is_a($class, QueryHandler::class, true)) {
                    $this->app->tag($class, 'dba_ddd.query_handler');
                } elseif (is_a($class, DomainEventSubscriber::class, true)) {
                    $this->app->tag($class, 'dba_ddd.domain_event_subscriber');
                }
            }
        }
    }

    private function getClassFromFile(string $path): ?string
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            return null;
        }

        if (! preg_match('/namespace\s+(.+);/i', $contents, $matches)) {
            return null;
        }

        $namespace = $matches[1];
        $class = basename($path, '.php');

        return $namespace.'\\'.$class;
    }
}
