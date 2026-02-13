<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Persistence\Eloquent;

use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filter;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterField;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterGroup;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterOperator;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderBy;
use Dba\DddSkeleton\Shared\Infrastructure\Persistence\Eloquent\EloquentCriteria;

final class EloquentCriteriaConverter
{
    private $eloquent_criteria;

    public function __construct(
        private readonly Criteria $criteria,
        private readonly array $criteriaToEloquentFields = [],
        private readonly array $hydrators = []
    ) {}

    public static function convert(
        Criteria $criteria,
        array $criteriaToEloquentFields = [],
        array $hydrators = []
    ) {
        $converter = new self($criteria, $criteriaToEloquentFields, $hydrators);

        return $converter->convertToEloquentCriteria();
    }

    private function convertToEloquentCriteria(): EloquentCriteria
    {
        $this->eloquent_criteria = EloquentCriteria::create();
        $this->buildExpression($this->criteria);
        $this->formatOrder($this->criteria);
        if ($this->criteria->offset())
            $this->eloquent_criteria->offset($this->criteria->offset());
        if ($this->criteria->limit())
            $this->eloquent_criteria->limit($this->criteria->limit());
        return $this->eloquent_criteria;
    }

    private function buildExpression(Criteria $criteria): void
    {
        if ($criteria->hasFilters()) {
            array_map($this->buildComparison(), $criteria->plainFilters());
        }
    }

    private function buildComparison(): callable
    {
        return function ($filter_or_group) {
            if ($filter_or_group instanceof FilterGroup) {
                $glue = $this->criteria->glue() === 'or' ? 'orWhere' : 'where';

                $this->eloquent_criteria->{$glue}(function ($query) use ($filter_or_group) {
                    foreach ($filter_or_group->filters() as $index => $filter) {
                        $this->applyFilter($query, $filter, $index === 0 ? 'where' : $filter_or_group->glue());
                    }
                });
            } else {
                $this->applyFilter($this->eloquent_criteria, $filter_or_group, $this->criteria->glue());
            }
        };
    }

    private function applyFilter($query, Filter $filter, ?string $glue = 'and')
    {
        $glueMethod = $glue === 'or' ? 'orWhere' : 'where';
        $field = $this->mapFieldValue($filter->field());
        $value = $this->existsHydratorFor($field)
            ? $this->hydrate($field, $filter->value()->value())
            : $filter->value()->value();

        if ($filter->operator()->value() === FilterOperator::CONTAINS) {
            $query->{$glueMethod}($field, 'LIKE', '%' . $value . '%');
        } elseif ($filter->operator()->value() === FilterOperator::NOT_CONTAINS) {
            $query->{$glueMethod}($field, 'NOT LIKE', '%' . $value . '%');
        } elseif ($filter->operator()->value() === FilterOperator::IN) {
            $query->{$glueMethod . 'In'}($field, explode(',', $value));
        } elseif ($filter->operator()->value() === FilterOperator::BETWEEN) {
            $values = explode(',', $value);
            if (count($values) === 2) {
                $query->{$glueMethod . 'Between'}($field, [$values[0], $values[1]]);
            } else {
                throw new \InvalidArgumentException('BETWEEN operator requires two values');
            }
        } elseif ($filter->operator()->value() === FilterOperator::STARTS_WITH) {
            $query->{$glueMethod}($field, 'LIKE', $value . '%');
        } elseif ($filter->operator()->value() === FilterOperator::ENDS_WITH) {
            $query->{$glueMethod}($field, 'LIKE', '%' . $value);
        } elseif ($filter->operator()->value() === FilterOperator::GT) {
            $query->{$glueMethod}($field, '>', $value);
        } elseif ($filter->operator()->value() === FilterOperator::LT) {
            $query->{$glueMethod}($field, '<', $value);
        } elseif ($filter->operator()->value() === FilterOperator::GTE) {
            $query->{$glueMethod}($field, '>=', $value);
        } elseif ($filter->operator()->value() === FilterOperator::LTE) {
            $query->{$glueMethod}($field, '<=', $value);
        } elseif (($filter->operator()->value() === FilterOperator::EQUAL || $filter->operator()->value() === FilterOperator::NOT_EQUAL) && $value == '') {
            if ($filter->operator()->value() === FilterOperator::EQUAL) {
                $query->{$glueMethod . 'Null'}($field);
            } elseif ($filter->operator()->value() === FilterOperator::NOT_EQUAL) {
                $query->{$glueMethod . 'NotNull'}($field);
            }
        } else {
            $query->{$glueMethod}($field, $filter->operator()->value(), $value);
        }
    }

    private function mapFieldValue(FilterField $field)
    {
        return array_key_exists($field->value(), $this->criteriaToEloquentFields)
            ? $this->criteriaToEloquentFields[$field->value()]
            : $field->value();
    }

    private function formatOrder(Criteria $criteria): void
    {
        $order = $criteria->order();

        // Si expose 'orders()', procesamos múltiple
        if (method_exists($order, 'orders')) {
            foreach ($order->orders() as $pair) {
                $field = $this->mapOrderBy($pair['orderBy']);
                if ($field) {
                    $this->eloquent_criteria->orderBy($field, $pair['orderType']->value());
                }
            }
            return;
        }

        /* legacy single-field (por si estás en una versión antigua de Order) */
        if ($field = $this->mapOrderBy($order->orderBy())) {
            $this->eloquent_criteria->orderBy($field, $order->orderType()->value());
        }
    }

    private function mapOrderBy(OrderBy $field)
    {
        return array_key_exists($field->value(), $this->criteriaToEloquentFields)
            ? $this->criteriaToEloquentFields[$field->value()]
            : $field->value();
    }

    private function existsHydratorFor($field): bool
    {
        return array_key_exists($field, $this->hydrators);
    }

    private function hydrate($field, string $value)
    {
        return $this->hydrators[$field]($value);
    }
}
