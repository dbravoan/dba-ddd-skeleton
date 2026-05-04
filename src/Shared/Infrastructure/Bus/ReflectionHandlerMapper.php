<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus;

use ReflectionClass;
use ReflectionNamedType;

trait ReflectionHandlerMapper
{
    /** @var array<string, callable> */
    private array $mappedHandlers = [];

    /**
     * @param  iterable<callable>  $handlers
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
}
