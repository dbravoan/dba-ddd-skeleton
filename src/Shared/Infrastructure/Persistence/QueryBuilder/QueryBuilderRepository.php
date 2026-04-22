<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Persistence\QueryBuilder;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

abstract class QueryBuilderRepository
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param string $method
     * @param array<mixed> $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        /** @var callable $callable */
        $callable = [$this->queryBuilder, $method];
        $result = call_user_func_array($callable, $arguments);

        if ($result instanceof QueryBuilder) {
            $this->queryBuilder = $result;

            return $this;
        }

        return $result;
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  QueryBuilderCriteria|QueryBuilderCriteria[]|array<mixed>  $criteria
     * @return mixed
     */
    public function first(QueryBuilderCriteria|array $criteria = [])
    {
        return $this->matching($criteria)->first();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  QueryBuilderCriteria|QueryBuilderCriteria[]|array<mixed>  $criteria
     * @return Collection<int|string, mixed>
     */
    public function get(QueryBuilderCriteria|array $criteria = [])
    {
        /** @var Collection<int|string, mixed> $collection */
        $collection = $this->matching($criteria)->get();

        return $collection;
    }

    /**
     * matching.
     *
     * @param  QueryBuilderCriteria|QueryBuilderCriteria[]|array<mixed>  $criteria
     * @return QueryBuilder
     */
    public function matching(QueryBuilderCriteria|array $criteria)
    {
        /** @var QueryBuilderCriteria[] $criteriaList */
        $criteriaList = is_array($criteria) ? $criteria : [$criteria];

        return array_reduce($criteriaList, function (QueryBuilder $query, QueryBuilderCriteria $criteria) {
            $criteria->each(function (Method $method) use ($query) {
                /** @var callable $callable */
                $callable = [$query, $method->name];
                /** @var array<int, mixed> $parameters */
                $parameters = $method->parameters;
                call_user_func_array($callable, $parameters);
            });

            return $query;
        }, clone $this->queryBuilder);
    }

    /**
     * Get a new query builder for the table.
     *
     * @return QueryBuilder
     */
    public function newQuery()
    {
        return clone $this->queryBuilder;
    }
}
