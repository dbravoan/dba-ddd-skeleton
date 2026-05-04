<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Persistence\QueryBuilder;

final class Method
{
    public function __construct(
        public readonly string $name,
        public readonly mixed $parameters = []
    ) {}
}
