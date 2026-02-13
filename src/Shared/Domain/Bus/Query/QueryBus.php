<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Bus\Query;

interface QueryBus
{
    public function ask(Query $query): ?Response;
}
