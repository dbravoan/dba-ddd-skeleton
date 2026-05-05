<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Persistence\QueryBuilder;

use Illuminate\Database\Query\Expression as QueryExpression;

/**
 * Wrapper around Laravel's Query Expression that guarantees a string cast
 * and a typed getValue() return.
 *
 * @extends QueryExpression<string|int|float>
 */
class Expression extends QueryExpression
{
    /**
     * Get the value of the expression.
     */
    public function __toString(): string
    {
        return (string) $this->getValue();
    }

    /**
     * Get the value of the expression.
     *
     * @return float|int|string|\Stringable
     */
    public function getValue(mixed $grammar = null): mixed
    {
        return $this->value;
    }
}
