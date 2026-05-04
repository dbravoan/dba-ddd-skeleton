<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Query;

use Dba\DddSkeleton\Shared\Domain\Bus\Query\Query;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\QueryBus;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\Response;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\ReflectionHandlerMapper;

final class LaravelQueryBus implements QueryBus
{
    use ReflectionHandlerMapper;

    /**
     * @param  iterable<callable>  $queryHandlers
     */
    public function __construct(iterable $queryHandlers)
    {
        $this->mapHandlers($queryHandlers);
    }

    public function ask(Query $query): ?Response
    {
        $handler = $this->mappedHandlers[$query::class] ?? null;

        if (! $handler) {
            throw new QueryNotRegisteredError($query);
        }

        /** @var Response|null $response */
        $response = $handler($query);

        return $response;
    }
}
