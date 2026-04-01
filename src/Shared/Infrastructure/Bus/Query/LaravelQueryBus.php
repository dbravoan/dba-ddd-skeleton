<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Query;

use Dba\DddSkeleton\Shared\Domain\Bus\Query\Query;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\QueryBus;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\Response;
use ReflectionClass;

final class LaravelQueryBus implements QueryBus
{
    private array $mappedHandlers = [];

    public function __construct(iterable $queryHandlers)
    {
        $this->mapHandlers($queryHandlers);
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

    public function ask(Query $query): ?Response
    {
        $handler = $this->mappedHandlers[$query::class] ?? null;
        
        if (!$handler) {
            throw new QueryNotRegisteredError($query);
        }

        /** @var Response|null $response */
        $response = $handler($query);

        return $response;
    }
}