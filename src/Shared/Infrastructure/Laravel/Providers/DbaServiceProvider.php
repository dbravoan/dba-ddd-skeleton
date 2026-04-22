<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;
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

        $finder = new Finder();
        $finder->files()->in($path)->name('*Handler.php');

        foreach ($finder as $file) {
            $class = $this->getClassFromFile($file->getRealPath());

            if ($class && class_exists($class)) {
                $this->app->tag($class, 'dba_handler');
                
                // If it's a CommandHandler, we might want to register it specifically if we have a custom bus
                // that uses tags. But our LaravelCommandBus uses constructor injection of an iterable.
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
