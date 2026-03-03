<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Query;

use Dba\DddSkeleton\Shared\Domain\Bus\Query\Query;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\QueryBus;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\Response;
use Illuminate\Contracts\Bus\Dispatcher;
use ReflectionClass;
use RuntimeException;

final class LaravelQueryBus implements QueryBus
{
    private array $mappedHandlers = [];

    public function __construct(
        private readonly Dispatcher $dispatcher,
        iterable $queryHandlers
    ) {
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
            throw new RuntimeException(sprintf('No handler found for query %s', $query::class));
        }

        /** @var Response|null $response */
        $response = $handler($query);

        return $response;
    }
}