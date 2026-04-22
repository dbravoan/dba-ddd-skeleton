<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class MakeModuleCommand extends Command
{
    protected $signature = 'dba:make:module {context : The name of the Bounded Context} {module : The name of the Module}
                            {--root-namespace= : The root namespace (default: Dba\DddSkeleton)}
                            {--no-finder : Skip generating Finder classes}
                            {--no-creator : Skip generating Creator classes}
                            {--no-updater : Skip generating Updater classes}
                            {--no-searcher : Skip generating Searcher classes}
                            {--application-service : Generate Application Service instead of putting logic in Handler}';

    protected $description = 'Create a new DDD Module structure';

    public function handle(): void
    {
        $contextArg = $this->argument('context');
        $moduleArg = $this->argument('module');

        $context = Str::studly(is_string($contextArg) ? $contextArg : '');
        $module = Str::studly(is_string($moduleArg) ? $moduleArg : '');

        $basePath = "src/{$context}/{$module}";

        if (File::exists($basePath)) {
            $this->error("Module {$module} already exists in {$context}!");

            return;
        }

        $this->createDirectories($basePath);
        $this->createDomainClasses($context, $module, $basePath);
        $this->createApplicationClasses($context, $module, $basePath);
        $this->createInfrastructureClasses($context, $module, $basePath);
        $this->createTests($context, $module, $basePath);

        $this->info("Module {$module} created successfully in {$context}.");

        $this->comment('Remember to register your Repository in your ServiceProvider:');
        $this->comment("    \$this->app->bind(\\\\{$context}\\\\{$module}\\\\Domain\\\\{$module}Repository::class, \\\\{$context}\\\\{$module}\\\\Infrastructure\\\\Persistence\\\\Eloquent{$module}Repository::class);");
    }

    private function createDirectories(string $basePath): void
    {
        $directories = [
            "$basePath/Application",
            "$basePath/Domain",
            "$basePath/Infrastructure/Persistence",
            "$basePath/Infrastructure/Controller",
            "$basePath/Tests/Domain",
            "$basePath/Tests/Application",
        ];

        foreach ($directories as $dir) {
            File::makeDirectory($dir, 0755, true);
        }
    }

    private function createDomainClasses(string $context, string $module, string $basePath): void
    {
        // Entity
        $this->createFile("$basePath/Domain/{$module}.php", $this->getStub('Entity', $context, $module));
        // Domain Event
        $this->createFile("$basePath/Domain/{$module}CreatedDomainEvent.php", $this->getStub('CreatedDomainEvent', $context, $module));
        // Value Objects
        $this->createFile("$basePath/Domain/{$module}Id.php", $this->getStub('ValueObject', $context, $module, ['name' => "{$module}Id", 'type' => 'Uuid']));
        $this->createFile("$basePath/Domain/{$module}Name.php", $this->getStub('ValueObject', $context, $module, ['name' => "{$module}Name", 'type' => 'StringValueObject']));
        // Repository Interface
        $this->createFile("$basePath/Domain/{$module}Repository.php", $this->getStub('RepositoryInterface', $context, $module));
    }

    private function createApplicationClasses(string $context, string $module, string $basePath): void
    {
        File::makeDirectory("$basePath/Application/Response", 0755, true);
        $this->createFile("$basePath/Application/Response/{$module}Response.php", $this->getStub('Response', $context, $module));
        $this->createFile("$basePath/Application/Response/{$module}sResponse.php", $this->getStub('Responses', $context, $module));

        if (! $this->option('no-creator')) {
            File::makeDirectory("$basePath/Application/Create", 0755, true);
            $this->createFile("$basePath/Application/Create/Create{$module}Command.php", $this->getStub('CreateCommand', $context, $module));

            if ($this->option('application-service')) {
                $this->createFile("$basePath/Application/Create/{$module}Creator.php", $this->getStub('Creator', $context, $module));
                $this->createFile("$basePath/Application/Create/Create{$module}CommandHandler.php", $this->getStub('CreateCommandHandlerWithService', $context, $module));
            } else {
                $this->createFile("$basePath/Application/Create/Create{$module}CommandHandler.php", $this->getStub('CreateCommandHandler', $context, $module));
            }
        }

        if (! $this->option('no-finder')) {
            File::makeDirectory("$basePath/Application/Find", 0755, true);
            $this->createFile("$basePath/Application/Find/Find{$module}Query.php", $this->getStub('FindQuery', $context, $module));
            $this->createFile("$basePath/Application/Find/Find{$module}QueryHandler.php", $this->getStub('FindQueryHandler', $context, $module));
        }

        if (! $this->option('no-updater')) {
            File::makeDirectory("$basePath/Application/Update", 0755, true);
            $this->createFile("$basePath/Application/Update/Update{$module}Command.php", $this->getStub('UpdateCommand', $context, $module));
            $this->createFile("$basePath/Application/Update/Update{$module}CommandHandler.php", $this->getStub('UpdateCommandHandler', $context, $module));
        }

        if (! $this->option('no-searcher')) {
            File::makeDirectory("$basePath/Application/SearchByCriteria", 0755, true);
            $this->createFile("$basePath/Application/SearchByCriteria/Search{$module}sByCriteriaQuery.php", $this->getStub('SearchByCriteriaQuery', $context, $module));
            $this->createFile("$basePath/Application/SearchByCriteria/Search{$module}sByCriteriaQueryHandler.php", $this->getStub('SearchByCriteriaQueryHandler', $context, $module));
        }
    }

    private function createInfrastructureClasses(string $context, string $module, string $basePath): void
    {
        // Persistence
        $this->createFile("$basePath/Infrastructure/Persistence/Eloquent{$module}Repository.php", $this->getStub('EloquentRepository', $context, $module));

        // Controllers
        if (! $this->option('no-creator')) {
            $this->createFile("$basePath/Infrastructure/Controller/{$module}PostController.php", $this->getStub('CreateController', $context, $module));
        }
        if (! $this->option('no-finder')) {
            $this->createFile("$basePath/Infrastructure/Controller/{$module}GetController.php", $this->getStub('FindController', $context, $module));
        }
        if (! $this->option('no-updater')) {
            $this->createFile("$basePath/Infrastructure/Controller/{$module}PutController.php", $this->getStub('UpdateController', $context, $module));
        }
        if (! $this->option('no-searcher')) {
            $this->createFile("$basePath/Infrastructure/Controller/{$module}sGetController.php", $this->getStub('SearchByCriteriaController', $context, $module));
        }

        $this->createFile("$basePath/Infrastructure/Controller/{$module}DeleteController.php", $this->getStub('DeleteController', $context, $module));
    }

    private function createTests(string $context, string $module, string $basePath): void
    {
        $this->createFile("$basePath/Tests/Domain/{$module}Test.php", $this->getStub('UnitTest', $context, $module));
    }

    private function createFile(string $path, string $content): void
    {
        File::put($path, $content);
    }

    /**
     * @param array<string, string> $replacements
     */
    private function getStub(string $name, string $context, string $module, array $replacements = []): string
    {
        $path = __DIR__."/Stubs/{$name}.stub";
        $content = File::get($path);

        $replacements = array_merge([
            'namespace' => "{$this->rootNamespace()}\\{$context}\\{$module}",
            'context'   => $context,
            'module'    => $module,
            'module_lc' => strtolower($module),
        ], $replacements);

        foreach ($replacements as $key => $value) {
            $content = str_replace('{{'.$key.'}}', $value, $content);
        }

        return $content;
    }

    private function rootNamespace(): string
    {
        $namespace = $this->option('root-namespace');

        return is_string($namespace) ? $namespace : 'Dba\\DddSkeleton';
    }
}
