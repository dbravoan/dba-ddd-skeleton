<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Command;

use Dba\DddSkeleton\Shared\Domain\Bus\Command\Command;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandBus;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\CallableFirstParameterExtractor;
use Illuminate\Contracts\Bus\Dispatcher;

final class LaravelCommandBus implements CommandBus
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
        iterable $commandHandlers
    ) {
        $this->dispatcher->map(
            CallableFirstParameterExtractor::forCallables($commandHandlers)
        );
    }

    public function dispatch(Command $command): void
    {
        $this->dispatcher->dispatchSync($command);
    }
}
