<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Infrastructure\Bus\Command;

use Dba\DddSkeleton\Shared\Domain\Bus\Command\Command;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\Command\CommandNotRegisteredError;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\Command\LaravelCommandBus;
use PHPUnit\Framework\TestCase;

final class LaravelCommandBusTest extends TestCase
{
    /** @test */
    public function it_should_dispatch_a_command_to_its_handler(): void
    {
        $command = new StubCommand;
        $handler = new StubCommandHandler;
        $bus = new LaravelCommandBus([$handler]);

        $bus->dispatch($command);

        $this->assertTrue($handler->hasBeenCalled());
    }

    /** @test */
    public function it_should_throw_exception_if_handler_is_not_registered(): void
    {
        $this->expectException(CommandNotRegisteredError::class);

        $command = new StubCommand;
        $bus = new LaravelCommandBus([]);

        $bus->dispatch($command);
    }
}

final class StubCommand implements Command {}

final class StubCommandHandler
{
    private bool $called = false;

    public function __invoke(StubCommand $command): void
    {
        $this->called = true;
    }

    public function hasBeenCalled(): bool
    {
        return $this->called;
    }
}
