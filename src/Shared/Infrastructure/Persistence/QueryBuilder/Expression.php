<?php

namespace Dba\DddSkeleton\Shared\Infrastructure\Persistence\QueryBuilder;

use Illuminate\Database\Query\Expression as QueryExpression;

/**
 * @extends QueryExpression<float|int|string>
 */
class Expression extends QueryExpression
{
    /**
     * Create a new raw query expression.
     *
     * @param  float|int|string|\Stringable  $value
     */
    public function __construct($value)
    {
        parent::__construct($value);
    }

    /**
     * Get the value of the expression.
     *
     * @return string
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
