<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Command;

use Dba\DddSkeleton\Shared\Domain\Bus\Command\Command;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandBus;
use ReflectionClass;

final class LaravelCommandBus implements CommandBus
{
    private array $mappedHandlers = [];

    public function __construct(iterable $commandHandlers)
    {
        $this->mapHandlers($commandHandlers);
    }

    private function mapHandlers(iterable $handlers): void
    {
        foreach ($handlers as $handler) {
            $reflector = new ReflectionClass($handler);
            $method = $reflector->getMethod('__invoke');
            
            if ($method->getNumberOfParameters() === 1) {
                $paramType = $method->getParameters()[0]->getType()?->getName();
                if ($paramType) {
                    $this->mappedHandlers[$paramType] = $handler;
                }
            }
        }
    }

    public function dispatch(Command $command): void
    {
        $handler = $this->mappedHandlers[$command::class] ?? null;
        
        if (!$handler) {
            throw new CommandNotRegisteredError($command);
        }

        $handler($command);
    }
}