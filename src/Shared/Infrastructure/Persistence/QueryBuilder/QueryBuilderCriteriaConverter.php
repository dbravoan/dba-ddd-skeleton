<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Persistence\QueryBuilder;

use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filter;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterField;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterGroup;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterOperator;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderBy;
use Illuminate\Database\Query\Builder as QueryBuilder;

final class QueryBuilderCriteriaConverter
{
    private QueryBuilderCriteria $query_builder_criteria;

    /**
     * @param array<string, string> $criteriaToQueryBuilderFields
     * @param array<string, callable> $hydrators
     */
    public function __construct(
        private readonly Criteria $criteria,
        private readonly array $criteriaToQueryBuilderFields = [],
        private readonly array $hydrators = []
    ) {}

    /**
     * @param array<string, string> $criteriaToQueryBuilderFields
     * @param array<string, callable> $hydrators
     */
    public static function convert(
        Criteria $criteria,
        array $criteriaToQueryBuilderFields = [],
        array $hydrators = []
    ): QueryBuilderCriteria {
        $converter = new self($criteria, $criteriaToQueryBuilderFields, $hydrators);

        return $converter->convertToQueryBuilderCriteria();
    }

    private function convertToQueryBuilderCriteria(): QueryBuilderCriteria
    {
        $this->query_builder_criteria = QueryBuilderCriteria::create();
        $this->buildExpression($this->criteria);
        $this->formatOrder($this->criteria);
        if ($this->criteria->offset()) {
            $this->query_builder_criteria->offset($this->criteria->offset());
        }
        if ($this->criteria->limit()) {
            $this->query_builder_criteria->limit($this->criteria->limit());
        }

        return $this->query_builder_criteria;
    }

    private function buildExpression(Criteria $criteria): void
    {
        if ($criteria->hasFilters()) {
            array_map($this->buildComparison(), $criteria->plainFilters());
        }
    }

    private function buildComparison(): callable
    {
        return function (Filter|FilterGroup $filter_or_group): void {
            if ($filter_or_group instanceof FilterGroup) {
                $glue = $this->criteria->glue() === 'or' ? 'orWhere' : 'where';

                $this->query_builder_criteria->{$glue}(function (mixed $query) use ($filter_or_group): void {
                    foreach ($filter_or_group->filters() as $index => $filter) {
                        $this->applyFilter($query, $filter, $index === 0 ? 'where' : $filter_or_group->glue());
                    }
                });
            } else {
                $this->applyFilter($this->query_builder_criteria, $filter_or_group, $this->criteria->glue());
            }
        };
    }

    private function applyFilter(mixed $query, Filter $filter, ?string $glue = 'and'): void
    {
        /** @var QueryBuilder $query */
        $glueMethod = $glue === 'or' ? 'orWhere' : 'where';
        $field = $this->mapFieldValue($filter->field());

        $valueRaw = $filter->value()->value();
        /** @var string $valueString */
        $valueString = is_scalar($valueRaw) ? (string) $valueRaw : '';

        if ($this->existsHydratorFor($field)) {
            $hydrated = $this->hydrate($field, $valueString);
            $value = is_scalar($hydrated) ? (string) $hydrated : '';
        } else {
            $value = $valueString;
        }

        if ($filter->operator()->value() === FilterOperator::CONTAINS) {
            $query->{$glueMethod}($field, 'LIKE', '%'.$value.'%');
        } elseif ($filter->operator()->value() === FilterOperator::NOT_CONTAINS) {
            $query->{$glueMethod}($field, 'NOT LIKE', '%'.$value.'%');
        } elseif ($filter->operator()->value() === FilterOperator::IN) {
            $query->{$glueMethod.'In'}($field, explode(',', $value));
        } elseif ($filter->operator()->value() === FilterOperator::NOT_IN) {
            $query->{$glueMethod.'NotIn'}($field, explode(',', $value));
        } elseif ($filter->operator()->value() === FilterOperator::BETWEEN) {
            $values = explode(',', $value);
            if (count($values) === 2) {
                $query->{$glueMethod.'Between'}($field, [$values[0], $values[1]]);
            } else {
                throw new \InvalidArgumentException('BETWEEN operator requires two values');
            }
        } elseif ($filter->operator()->value() === FilterOperator::NOT_BETWEEN) {
            $values = explode(',', $value);
            if (count($values) === 2) {
                $query->{$glueMethod.'NotBetween'}($field, [$values[0], $values[1]]);
            } else {
                throw new \InvalidArgumentException('NOT BETWEEN operator requires two values');
            }
        } elseif ($filter->operator()->value() === FilterOperator::STARTS_WITH) {
            $query->{$glueMethod}($field, 'LIKE', $value.'%');
        } elseif ($filter->operator()->value() === FilterOperator::ENDS_WITH) {
            $query->{$glueMethod}($field, 'LIKE', '%'.$value);
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
                $query->{$glueMethod.'Null'}($field);
            } elseif ($filter->operator()->value() === FilterOperator::NOT_EQUAL) {
                $query->{$glueMethod.'NotNull'}($field);
            }
        } elseif ($filter->operator()->value() === FilterOperator::IS_NULL) {
            $query->{$glueMethod.'Null'}($field);
        } elseif ($filter->operator()->value() === FilterOperator::IS_NOT_NULL) {
            $query->{$glueMethod.'NotNull'}($field);
        } else {
            $query->{$glueMethod}($field, $filter->operator()->value(), $value);
        }
    }

    private function mapFieldValue(FilterField $field): string
    {
        return array_key_exists($field->value(), $this->criteriaToQueryBuilderFields)
            ? $this->criteriaToQueryBuilderFields[$field->value()]
            : $field->value();
    }

    private function formatOrder(Criteria $criteria): void
    {
        $order = $criteria->order();

        if ($order_by = $this->mapOrderBy($order->orderBy())) {
            $this->query_builder_criteria->orderBy($order_by, $order->orderType()->value());
        }
    }

    private function mapOrderBy(OrderBy $field): string
    {
        return array_key_exists($field->value(), $this->criteriaToQueryBuilderFields)
            ? $this->criteriaToQueryBuilderFields[$field->value()]
            : $field->value();
    }

    private function existsHydratorFor(string $field): bool
    {
        return array_key_exists($field, $this->hydrators);
    }

    private function hydrate(string $field, string $value): mixed
    {
        return $this->hydrators[$field]($value);
    }
}
