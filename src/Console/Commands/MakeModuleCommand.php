<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

final class MakeModuleCommand extends Command
{
    protected $signature = 'dba:make:module {context : The name of the Bounded Context} {module : The name of the Module}
                            {--no-finder : Skip generating Finder classes}
                            {--no-creator : Skip generating Creator classes}
                            {--no-updater : Skip generating Updater classes}
                            {--no-searcher : Skip generating Searcher classes}';

    protected $description = 'Create a new DDD Module structure';

    public function handle(): void
    {
        $context = Str::studly($this->argument('context'));
        $module = Str::studly($this->argument('module'));

        $basePath = "src/{$context}/{$module}";

        if (File::exists($basePath)) {
            $this->error("Module {$module} in context {$context} already exists!");
            return;
        }

        $this->createDirectories($basePath);
        $this->createDomainClasses($context, $module, $basePath);
        $this->createApplicationClasses($context, $module, $basePath);
        $this->createInfrastructureClasses($context, $module, $basePath);

        $this->info("Module {$module} created successfully in {$context}.");
        $this->warn("IMPORTANT: Don't forget to bind the Repository Interface in your ServiceProvider!");
        $this->line("Example Config in AppServiceProvider or RepositoryServiceProvider:");
        $this->comment("    \$this->app->bind(\\{$context}\\{$module}\\Domain\\{$module}Repository::class, \\{$context}\\{$module}\\Infrastructure\\Persistence\\Eloquent{$module}Repository::class);");
        $this->line("Otherwise, you must manually instantiate the repository (new Eloquent{$module}Repository) in your codebase.");
    }

    private function createDirectories(string $basePath): void
    {
        $directories = [
            "$basePath/Application",
            "$basePath/Domain",
            "$basePath/Infrastructure/Persistence",
            "$basePath/Infrastructure/Controller",
        ];

        foreach ($directories as $dir) {
            File::makeDirectory($dir, 0755, true);
        }
    }

    private function createDomainClasses(string $context, string $module, string $basePath): void
    {
        // Entity
        $this->createFile("$basePath/Domain/{$module}.php", $this->getStub('Entity', $context, $module));
        // Value Objects
        $this->createFile("$basePath/Domain/{$module}Id.php", $this->getStub('ValueObject', $context, $module, ['Type' => 'Id']));
        $this->createFile("$basePath/Domain/{$module}Name.php", $this->getStub('ValueObject', $context, $module, ['Type' => 'Name']));
        // Repository Interface
        $this->createFile("$basePath/Domain/{$module}Repository.php", $this->getStub('RepositoryInterface', $context, $module));
    }

    private function createApplicationClasses(string $context, string $module, string $basePath): void
    {
        // Responses
        File::makeDirectory("$basePath/Application/Response", 0755, true);
        $this->createFile("$basePath/Application/Response/{$module}Response.php", $this->getStub('Response', $context, $module));
        $this->createFile("$basePath/Application/Response/{$module}sResponse.php", $this->getStub('Responses', $context, $module));

        if (!$this->option('no-creator')) {
            File::makeDirectory("$basePath/Application/Create", 0755, true);
            $this->createFile("$basePath/Application/Create/Create{$module}Command.php", $this->getStub('CreateCommand', $context, $module));
            $this->createFile("$basePath/Application/Create/Create{$module}CommandHandler.php", $this->getStub('CreateCommandHandler', $context, $module));
        }

        if (!$this->option('no-finder')) {
            File::makeDirectory("$basePath/Application/Find", 0755, true);
            $this->createFile("$basePath/Application/Find/Find{$module}Query.php", $this->getStub('FindQuery', $context, $module));
            $this->createFile("$basePath/Application/Find/Find{$module}QueryHandler.php", $this->getStub('FindQueryHandler', $context, $module));
        }

        if (!$this->option('no-searcher')) {
            File::makeDirectory("$basePath/Application/SearchByCriteria", 0755, true);
            $this->createFile("$basePath/Application/SearchByCriteria/{$module}sByCriteriaSearcher.php", $this->getStub('Searcher', $context, $module));
            $this->createFile("$basePath/Application/SearchByCriteria/Search{$module}sByCriteriaQuery.php", $this->getStub('SearchByCriteriaQuery', $context, $module));
            $this->createFile("$basePath/Application/SearchByCriteria/Search{$module}sByCriteriaQueryHandler.php", $this->getStub('SearchByCriteriaQueryHandler', $context, $module));
            $this->createFile("$basePath/Application/SearchByCriteria/Count{$module}sByCriteriaQuery.php", $this->getStub('CountByCriteriaQuery', $context, $module));
            $this->createFile("$basePath/Application/SearchByCriteria/Count{$module}sByCriteriaQueryHandler.php", $this->getStub('CountByCriteriaQueryHandler', $context, $module));
        }

        if (!$this->option('no-updater')) {
            File::makeDirectory("$basePath/Application/Update", 0755, true);
            $this->createFile("$basePath/Application/Update/Update{$module}Command.php", $this->getStub('UpdateCommand', $context, $module));
            $this->createFile("$basePath/Application/Update/Update{$module}CommandHandler.php", $this->getStub('UpdateCommandHandler', $context, $module));
        }

        // Delete Logic
        File::makeDirectory("$basePath/Application/Delete", 0755, true);
        $this->createFile("$basePath/Application/Delete/Delete{$module}Command.php", $this->getStub('DeleteCommand', $context, $module));
        $this->createFile("$basePath/Application/Delete/Delete{$module}CommandHandler.php", $this->getStub('DeleteCommandHandler', $context, $module));
    }

    private function createInfrastructureClasses(string $context, string $module, string $basePath): void
    {
        $this->createFile("$basePath/Infrastructure/Persistence/Eloquent{$module}Repository.php", $this->getStub('EloquentRepository', $context, $module));

        if (!$this->option('no-creator')) {
            $this->createFile("$basePath/Infrastructure/Controller/Create{$module}Controller.php", $this->getStub('CreateController', $context, $module));
        }

        if (!$this->option('no-finder')) {
            $this->createFile("$basePath/Infrastructure/Controller/Find{$module}Controller.php", $this->getStub('FindController', $context, $module));
        }

        if (!$this->option('no-updater')) {
            $this->createFile("$basePath/Infrastructure/Controller/Update{$module}Controller.php", $this->getStub('UpdateController', $context, $module));
        }

        // Always delete
        $this->createFile("$basePath/Infrastructure/Controller/Delete{$module}Controller.php", $this->getStub('DeleteController', $context, $module));

        if (!$this->option('no-searcher')) {
            $this->createFile("$basePath/Infrastructure/Controller/Search{$module}sByCriteriaController.php", $this->getStub('SearchByCriteriaController', $context, $module));
        }
    }

    private function createFile(string $path, string $content): void
    {
        File::put($path, $content);
    }

    private function getStub(string $type, string $context, string $module, array $replacements = []): string
    {
        $stubPath = __DIR__ . "/Stubs/{$type}.stub";

        if (File::exists(resource_path("stubs/dbravoan/dba-ddd-skeleton/{$type}.stub"))) {
            $stubPath = resource_path("stubs/dbravoan/dba-ddd-skeleton/{$type}.stub");
        }

        if (!File::exists($stubPath)) {
            // Fallback for stubs not yet migrated to files during this refactor if any
            // For now all are migrated or inline, but let's assume valid path or error.
            return '';
        }

        $content = File::get($stubPath);

        $rootNamespace = $this->rootNamespace();

        $namespace = "{$rootNamespace}\\{$context}\\{$module}";

        $content = str_replace('{{namespace}}', $namespace, $content);
        $content = str_replace('{{context}}', $context, $content);
        $content = str_replace('{{module}}', $module, $content);
        $content = str_replace('{{module_lc}}', Str::camel($module), $content);

        foreach ($replacements as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }

    private function rootNamespace(): string
    {
        return $this->option('root-namespace') ?? 'Dba\DddSkeleton';
    }
}
