<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Query;

use Dba\DddSkeleton\Shared\Domain\Bus\Query\Query;
use RuntimeException;

final class QueryNotRegisteredError extends RuntimeException
{
    public function __construct(Query $query)
    {
        $queryClass = $query::class;

        parent::__construct(sprintf(
            'The query <%s> does not have a query handler associated. '.
            'Make sure you have registered the handler in your ServiceProvider and tagged it (if using automatic registration) '.
            'or passed it to the LaravelQueryBus constructor.',
            $queryClass
        ));
    }
}
