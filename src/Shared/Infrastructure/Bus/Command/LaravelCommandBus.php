<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Command;

use Dba\DddSkeleton\Shared\Domain\Bus\Command\Command;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandBus;
use ReflectionClass;
use ReflectionNamedType;

final class LaravelCommandBus implements CommandBus
{
    /** @var array<string, callable> */
    private array $mappedHandlers = [];

    /**
     * @param iterable<callable> $commandHandlers
     */
    public function __construct(iterable $commandHandlers)
    {
        $this->mapHandlers($commandHandlers);
    }

    /**
     * @param iterable<callable> $handlers
     */
    private function mapHandlers(iterable $handlers): void
    {
        foreach ($handlers as $handler) {
            /** @var object $handler */
            $reflector = new ReflectionClass($handler);
            $method = $reflector->getMethod('__invoke');

            if ($method->getNumberOfParameters() === 1) {
                $type = $method->getParameters()[0]->getType();
                if ($type instanceof ReflectionNamedType) {
                    /** @var callable $handler */
                    $this->mappedHandlers[$type->getName()] = $handler;
                }
            }
        }
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
