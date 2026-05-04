<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Command;

use Dba\DddSkeleton\Shared\Domain\Bus\Command\Command;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandBus;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\ReflectionHandlerMapper;

final class LaravelCommandBus implements CommandBus
{
    use ReflectionHandlerMapper;

    /**
     * @param  iterable<callable>  $commandHandlers
     */
    public function __construct(iterable $commandHandlers)
    {
        $this->mapHandlers($commandHandlers);
    }

    public function dispatch(Command $command): void
    {
        $handler = $this->mappedHandlers[$command::class] ?? null;

        if (! $handler) {
            throw new CommandNotRegisteredError($command);
        }

        $handler($command);
    }
}
