<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Criteria;

final class FilterOperator
{
    public const EQUAL = '=';
    public const NOT_EQUAL = '!=';
    public const GT = '>';
    public const LT = '<';
    public const GTE = '>=';
    public const LTE = '<=';
    public const IN = 'IN';
    public const NOT_IN = 'NOT IN';
    public const CONTAINS = 'CONTAINS';
    public const NOT_CONTAINS = 'NOT_CONTAINS';
    public const STARTS_WITH = 'STARTS_WITH';
    public const ENDS_WITH = 'ENDS_WITH';
    public const IS_NULL = 'IS_NULL';
    public const IS_NOT_NULL = 'IS_NOT_NULL';
    public const BETWEEN = 'BETWEEN';
    public const NOT_BETWEEN = 'NOT_BETWEEN';

    public function __construct(private readonly string $value) {}

    public function value(): string
    {
        return $this->value;
    }
}
