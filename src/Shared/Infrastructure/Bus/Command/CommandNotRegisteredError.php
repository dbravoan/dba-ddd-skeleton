<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Command;

use Dba\DddSkeleton\Shared\Domain\Bus\Command\Command;
use RuntimeException;

final class CommandNotRegisteredError extends RuntimeException
{
    public function __construct(Command $command)
    {
        $commandClass = $command::class;

        parent::__construct(sprintf(
            'The command <%s> does not have a command handler associated. '.
            'Make sure you have registered the handler in your ServiceProvider and tagged it (if using automatic registration) '.
            'or passed it to the LaravelCommandBus constructor.',
            $commandClass
        ));
    }
}
