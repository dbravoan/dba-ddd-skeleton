<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Console\Commands;

use Dba\DddSkeleton\Tests\DbaTestCase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;

final class MakeModuleCommandTest extends DbaTestCase
{
    private string $testBasePath;

    private string $testTestsPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testBasePath = base_path('src/TestContext/TestModule');
        $this->testTestsPath = base_path('tests/TestContext/TestModule');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (File::exists(base_path('src/TestContext'))) {
            File::deleteDirectory(base_path('src/TestContext'));
        }

        if (File::exists(base_path('tests/TestContext'))) {
            File::deleteDirectory(base_path('tests/TestContext'));
        }
    }

    #[Test]
    public function it_should_generate_module_structure(): void
    {
        $this->artisan('dba:make:module', [
            'context' => 'TestContext',
            'module' => 'TestModule',
        ])->assertExitCode(0);

        // Domain files
        $this->assertFileExists($this->testBasePath.'/Domain/TestModule.php');
        $this->assertFileExists($this->testBasePath.'/Domain/TestModuleId.php');
        $this->assertFileExists($this->testBasePath.'/Domain/TestModuleName.php');
        $this->assertFileExists($this->testBasePath.'/Domain/TestModuleRepository.php');
        $this->assertFileExists($this->testBasePath.'/Domain/TestModuleCreatedDomainEvent.php');

        // Application files
        $this->assertFileExists($this->testBasePath.'/Application/Create/CreateTestModuleCommand.php');
        $this->assertFileExists($this->testBasePath.'/Application/Create/CreateTestModuleCommandHandler.php');
        $this->assertFileExists($this->testBasePath.'/Application/Find/FindTestModuleQuery.php');
        $this->assertFileExists($this->testBasePath.'/Application/Find/FindTestModuleQueryHandler.php');
        $this->assertFileExists($this->testBasePath.'/Application/Update/UpdateTestModuleCommand.php');
        $this->assertFileExists($this->testBasePath.'/Application/Update/UpdateTestModuleCommandHandler.php');
        $this->assertFileExists($this->testBasePath.'/Application/Delete/DeleteTestModuleCommand.php');
        $this->assertFileExists($this->testBasePath.'/Application/Delete/DeleteTestModuleCommandHandler.php');
        $this->assertFileExists($this->testBasePath.'/Application/SearchByCriteria/SearchTestModulesByCriteriaQuery.php');
        $this->assertFileExists($this->testBasePath.'/Application/SearchByCriteria/CountTestModulesByCriteriaQuery.php');

        // Infrastructure files
        $this->assertFileExists($this->testBasePath.'/Infrastructure/Persistence/EloquentTestModuleRepository.php');
        $this->assertFileExists($this->testBasePath.'/Infrastructure/Controller/CreateTestModuleController.php');
        $this->assertFileExists($this->testBasePath.'/Infrastructure/Controller/FindTestModuleController.php');
        $this->assertFileExists($this->testBasePath.'/Infrastructure/Controller/UpdateTestModuleController.php');
        $this->assertFileExists($this->testBasePath.'/Infrastructure/Controller/DeleteTestModuleController.php');
    }

    #[Test]
    public function it_should_generate_tests_in_tests_directory(): void
    {
        $this->artisan('dba:make:module', [
            'context' => 'TestContext',
            'module' => 'TestModule',
        ])->assertExitCode(0);

        $this->assertFileExists($this->testTestsPath.'/Domain/TestModuleTest.php');
    }

    #[Test]
    public function it_should_not_generate_creator_when_no_creator_flag_set(): void
    {
        $this->artisan('dba:make:module', [
            'context' => 'TestContext',
            'module' => 'TestModule',
            '--no-creator' => true,
        ])->assertExitCode(0);

        $this->assertFileDoesNotExist($this->testBasePath.'/Application/Create/CreateTestModuleCommand.php');
    }

    #[Test]
    public function it_should_not_generate_deleter_when_no_deleter_flag_set(): void
    {
        $this->artisan('dba:make:module', [
            'context' => 'TestContext',
            'module' => 'TestModule',
            '--no-deleter' => true,
        ])->assertExitCode(0);

        $this->assertFileDoesNotExist($this->testBasePath.'/Infrastructure/Controller/DeleteTestModuleController.php');
    }

    #[Test]
    public function it_should_fail_when_module_already_exists(): void
    {
        $this->artisan('dba:make:module', [
            'context' => 'TestContext',
            'module' => 'TestModule',
        ]);

        $this->artisan('dba:make:module', [
            'context' => 'TestContext',
            'module' => 'TestModule',
        ])->assertExitCode(1);
    }
}
