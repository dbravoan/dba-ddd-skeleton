<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Query;

use Dba\DddSkeleton\Shared\Domain\Bus\Query\Query;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\QueryBus;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\Response;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\CallableFirstParameterExtractor;
use Illuminate\Contracts\Bus\Dispatcher;

final class LaravelQueryBus implements QueryBus
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
        iterable $queryHandlers
    ) {
        $this->dispatcher->map(
            CallableFirstParameterExtractor::forCallables($queryHandlers)
        );
    }

    public function ask(Query $query): ?Response
    {
        /** @var Response|null $response */
        $response = $this->dispatcher->dispatchSync($query);

        return $response;
    }
}
