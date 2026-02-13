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

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function __call($method, $arguments)
    {
        $result = call_user_func_array([$this->queryBuilder, $method], $arguments);

        if ($result instanceof QueryBuilder) {
            $this->queryBuilder = $result;
            return $this;
        }
        return $result;
    }

    /**
     * Execute the query and get the first result.
     *
     * @param Criteria[] $criteria
     * @return mixed
     */
    public function first($criteria = [])
    {
        return $this->matching($criteria)->first();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param Criteria[] $criteria
     * @return Collection
     */
    public function get($criteria = [])
    {
        return $this->matching($criteria)->get();
    }

    /**
     * matching.
     *
     * @param Criteria[] $criteria
     * @return QueryBuilder
     */
    public function matching($criteria)
    {
        $criteria = is_array($criteria) ? $criteria : [$criteria];

        return array_reduce($criteria, function ($query, $criteria) {
            $criteria->each(function ($method) use ($query) {
                call_user_func_array([$query, $method->name], $method->parameters);
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
