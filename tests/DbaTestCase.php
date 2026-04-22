<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests;

use Dba\DddSkeleton\DddSkeletonServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class DbaTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            DddSkeletonServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        // Add migrations if needed for testing
    }
}
