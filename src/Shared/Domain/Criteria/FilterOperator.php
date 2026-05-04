<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Criteria;

enum FilterOperator: string
{
    case EQUAL = '=';
    case NOT_EQUAL = '!=';
    case GT = '>';
    case LT = '<';
    case GTE = '>=';
    case LTE = '<=';
    case IN = 'IN';
    case NOT_IN = 'NOT IN';
    case CONTAINS = 'CONTAINS';
    case NOT_CONTAINS = 'NOT_CONTAINS';
    case STARTS_WITH = 'STARTS_WITH';
    case ENDS_WITH = 'ENDS_WITH';
    case IS_NULL = 'IS_NULL';
    case IS_NOT_NULL = 'IS_NOT_NULL';
    case BETWEEN = 'BETWEEN';
    case NOT_BETWEEN = 'NOT_BETWEEN';

    public function value(): string
    {
        return $this->value;
    }
}
