<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Persistence\QueryBuilder;

use Illuminate\Database\Query\Expression as QueryExpression;

/**
 * Wrapper around Laravel's Query Expression that guarantees a string cast
 * and a typed getValue() return. The @extends annotation uses the generic
 * form which is valid on Laravel 11+ (L10 stubs are not generic; the
 * generics.notGeneric PHPStan warning is suppressed in phpstan.neon).
 *
 * @extends QueryExpression<float|int|string>
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
