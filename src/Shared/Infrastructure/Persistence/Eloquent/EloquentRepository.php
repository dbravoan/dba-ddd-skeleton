<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Persistence\Eloquent;

use Dba\DddSkeleton\Shared\Domain\Aggregate\AggregateRoot;
use Dba\DddSkeleton\Shared\Domain\Bus\Event\EventBus;
use Dba\DddSkeleton\Shared\Infrastructure\Persistence\QueryBuilder\Method;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Throwable;

/**
 * @template TModel of Model
 */
abstract class EloquentRepository
{
    /**
     * @var TModel|Builder<TModel>
     */
    protected Model|Builder $model;

    /**
     * @param  TModel|Builder<TModel>  $model
     */
    public function __construct(Model|Builder $model, private readonly ?EventBus $eventBus = null)
    {
        $this->model = $model;
    }

    protected function publishEvents(AggregateRoot $aggregate): void
    {
        $events = $aggregate->pullDomainEvents();

        if ($this->eventBus !== null && count($events) > 0) {
            $this->eventBus->publish(...$events);
        }
    }

    /**
     * Find a model by its primary key.
     *
     * @param  array<int, string>  $columns
     * @return TModel|null
     */
    public function find(mixed $id, array $columns = ['*']): ?Model
    {
        /** @var TModel|null $result */
        $result = $this->newQuery()->find($id, $columns);

        return $result;
    }

    /**
     * Find multiple models by their primary keys.
     *
     * @param  Arrayable<int, mixed>|array<int, mixed>  $ids
     * @param  array<int, string>  $columns
     * @return EloquentCollection<int, TModel>
     */
    public function findMany(Arrayable|array $ids, array $columns = ['*']): EloquentCollection
    {
        /** @var EloquentCollection<int, TModel> $result */
        $result = $this->newQuery()->findMany($ids, $columns);

        return $result;
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param  array<int, string>  $columns
     * @return TModel
     *
     * @throws ModelNotFoundException
     */
    public function findOrFail(mixed $id, array $columns = ['*']): Model
    {
        /** @var TModel $result */
        $result = $this->newQuery()->findOrFail($id, $columns);

        return $result;
    }

    /**
     * Find a model by its primary key or return fresh model instance.
     *
     * @param  array<int, string>  $columns
     * @return TModel
     */
    public function findOrNew(mixed $id, array $columns = ['*']): Model
    {
        /** @var TModel $result */
        $result = $this->newQuery()->findOrNew($id, $columns);

        return $result;
    }

    /**
     * Get the first record matching the attributes or instantiate it.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $values
     * @return TModel
     */
    public function firstOrNew(array $attributes, array $values = []): Model
    {
        /** @var TModel $result */
        $result = $this->newQuery()->firstOrNew($attributes, $values);

        return $result;
    }

    /**
     * Get the first record matching the attributes or create it.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $values
     * @return TModel
     */
    public function firstOrCreate(array $attributes, array $values = []): Model
    {
        /** @var TModel $result */
        $result = $this->newQuery()->firstOrCreate($attributes, $values);

        return $result;
    }

    /**
     * Create or update a record matching the attributes, and fill it with values.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $values
     * @return TModel
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        /** @var TModel $result */
        $result = $this->newQuery()->updateOrCreate($attributes, $values);

        return $result;
    }

    /**
     * Execute the query and get the first result or throw an exception.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     * @param  array<int, string>  $columns
     * @return TModel
     *
     * @throws ModelNotFoundException
     */
    public function firstOrFail(EloquentCriteria|array $criteria = [], array $columns = ['*']): Model
    {
        /** @var TModel $result */
        $result = $this->matching($criteria)->firstOrFail($columns);

        return $result;
    }

    /**
     * create.
     *
     * @param  array<string, mixed>  $attributes
     * @return TModel
     *
     * @throws Throwable
     */
    public function create(array $attributes): Model
    {
        /** @var TModel $result */
        $result = $this->newQuery()->create($attributes);

        return $result;
    }

    /**
     * Save a new model and return the instance.
     *
     * @param  array<string, mixed>  $attributes
     * @return TModel
     *
     * @throws Throwable
     */
    public function forceCreate(array $attributes): Model
    {
        /** @var TModel $result */
        $result = $this->newQuery()->forceCreate($attributes);

        return $result;
    }

    /**
     * update.
     *
     * @param  array<string, mixed>  $attributes
     * @return TModel
     *
     * @throws Throwable
     */
    public function update(mixed $id, array $attributes): Model
    {
        /** @var TModel $model */
        $model = $this->findOrFail($id);

        tap($model, static function (Model $instance) use ($attributes) {
            $instance->fill($attributes)->saveOrFail();
        });

        return $model;
    }

    /**
     * forceUpdate.
     *
     * @param  array<string, mixed>  $attributes
     * @return TModel
     *
     * @throws Throwable
     */
    public function forceUpdate(mixed $id, array $attributes): Model
    {
        /** @var TModel $model */
        $model = $this->findOrFail($id);

        tap($model, static function (Model $instance) use ($attributes) {
            $instance->forceFill($attributes)->saveOrFail();
        });

        return $model;
    }

    /**
     * delete.
     */
    public function delete(mixed $id): ?bool
    {
        $model = $this->find($id);

        return $model?->delete();
    }

    /**
     * Restore a soft-deleted model instance.
     */
    public function restore(mixed $id): ?bool
    {
        /** @var callable $callable */
        $callable = [$this->newQuery()->where($this->getModel()->getKeyName(), $id), 'restore'];

        return (bool) call_user_func($callable);
    }

    /**
     * Force a hard delete on a soft deleted model.
     *
     * This method protects developers from running forceDelete when trait is missing.
     */
    public function forceDelete(mixed $id): ?bool
    {
        /** @var TModel $model */
        $model = $this->findOrFail($id);
        /** @var callable $callable */
        $callable = [$model, 'forceDelete'];

        return (bool) call_user_func($callable);
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function newInstance(array $attributes = [], bool $exists = false): Model
    {
        return $this->getModel()->newInstance($attributes, $exists);
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     * @param  array<int, string>  $columns
     * @return EloquentCollection<int, TModel>
     */
    public function get(EloquentCriteria|array $criteria = [], array $columns = ['*']): EloquentCollection
    {
        /** @var EloquentCollection<int, TModel> $result */
        $result = $this->matching($criteria)->get($columns);

        return $result;
    }

    /**
     * Chunk the results of the query.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     * @param  callable(Collection<int, TModel>, int): mixed  $callback
     */
    public function chunk(EloquentCriteria|array $criteria, int $count, callable $callback): bool
    {
        return $this->matching($criteria)->chunk($count, $callback);
    }

    /**
     * Execute a callback over each item while chunking.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     * @param  callable(TModel, int): bool  $callback
     */
    public function each(EloquentCriteria|array $criteria, callable $callback, int $count = 1000): bool
    {
        return $this->matching($criteria)->each($callback, $count);
    }

    /**
     * Execute the query and get the first result.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     * @param  array<int, string>  $columns
     * @return TModel|null
     */
    public function first(EloquentCriteria|array $criteria = [], array $columns = ['*']): ?Model
    {
        /** @var TModel|null $result */
        $result = $this->matching($criteria)->first($columns);

        return $result;
    }

    /**
     * Paginate the given query.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     * @param  array<int, string>  $columns
     * @return LengthAwarePaginator<int, TModel>
     *
     * @throws InvalidArgumentException
     */
    public function paginate(EloquentCriteria|array $criteria = [], ?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, TModel> $result */
        $result = $this->matching($criteria)->paginate($perPage, $columns, $pageName, $page);

        return $result;
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     * @param  array<int, string>  $columns
     * @return Paginator<int, TModel>
     */
    public function simplePaginate(EloquentCriteria|array $criteria = [], ?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): Paginator
    {
        /** @var Paginator<int, TModel> $result */
        $result = $this->matching($criteria)->simplePaginate($perPage, $columns, $pageName, $page);

        return $result;
    }

    /**
     * Retrieve the "count" result of the query.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     * @param  string|array<int, string>  $columns
     */
    public function count(EloquentCriteria|array $criteria = [], string|array $columns = '*'): int
    {
        if (is_array($columns)) {
            $columns = implode(',', $columns);
        }

        return (int) $this->matching($criteria)->count($columns);
    }

    /**
     * Retrieve the minimum value of a given column.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     */
    public function min(EloquentCriteria|array $criteria, string $column): float|int
    {
        $result = $this->matching($criteria)->min($column);

        return is_numeric($result) ? (float) $result : 0;
    }

    /**
     * Retrieve the maximum value of a given column.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     */
    public function max(EloquentCriteria|array $criteria, string $column): float|int
    {
        $result = $this->matching($criteria)->max($column);

        return is_numeric($result) ? (float) $result : 0;
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     */
    public function sum(EloquentCriteria|array $criteria, string $column): float|int
    {
        $result = $this->matching($criteria)->sum($column);

        return is_numeric($result) ? (float) $result : 0;
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     */
    public function avg(EloquentCriteria|array $criteria, string $column): float|int
    {
        $result = $this->matching($criteria)->avg($column);

        return is_numeric($result) ? (float) $result : 0;
    }

    /**
     * Alias for the "avg" method.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     */
    public function average(EloquentCriteria|array $criteria, string $column): float|int
    {
        return $this->avg($criteria, $column);
    }

    /**
     * matching.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     * @return Builder<TModel>
     */
    public function matching(EloquentCriteria|array $criteria): Builder
    {
        /** @var EloquentCriteria<TModel>[] $criteriaList */
        $criteriaList = is_array($criteria) ? $criteria : [$criteria];

        return array_reduce($criteriaList, static function (Builder $query, EloquentCriteria $criteria) {
            $criteria->each(static function (Method $method) use ($query) {
                /** @var callable $callable */
                $callable = [$query, $method->name];
                /** @var array<int, mixed> $parameters */
                $parameters = $method->parameters;
                call_user_func_array($callable, $parameters);
            });

            return $query;
        }, $this->newQuery());
    }

    /**
     * getQuery.
     *
     * @param  EloquentCriteria<TModel>|EloquentCriteria<TModel>[]|array<mixed>  $criteria
     */
    public function getQuery(EloquentCriteria|array $criteria = []): QueryBuilder
    {
        return $this->matching($criteria)->getQuery();
    }

    /**
     * getModel.
     *
     * @return TModel
     */
    public function getModel(): Model
    {
        /** @var TModel $model */
        $model = $this->model instanceof Model ? clone $this->model : $this->model->getModel();

        return $model;
    }

    /**
     * Get a new query builder for the model's table.
     *
     * @return Builder<TModel>
     */
    public function newQuery(): Builder
    {
        /** @var Builder<TModel> $query */
        $query = $this->model instanceof Model ? $this->model->newQuery() : clone $this->model;

        return $query;
    }
}
