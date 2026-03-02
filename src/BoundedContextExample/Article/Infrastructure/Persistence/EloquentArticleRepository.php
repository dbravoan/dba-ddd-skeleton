<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Infrastructure\Persistence;

use Dba\DddSkeleton\BoundedContextExample\Article\Domain\Article;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleId;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleRepository;
use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Infrastructure\Persistence\Eloquent\EloquentCriteriaConverter;
use Illuminate\Database\Eloquent\Model;

use function Lambdish\Phunctional\map;

use Dba\DddSkeleton\Shared\Infrastructure\Persistence\Eloquent\EloquentRepository;

final class EloquentArticleRepository extends EloquentRepository implements ArticleRepository
{
    private static array $toEloquentFields = [
        'id' => 'id',
        'name' => 'name',
        'price' => 'price',
        'stock' => 'stock',
    ];

    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function save(Article $article): void
    {
        $this->model->updateOrCreate(
            ['id' => $article->id()->value()],
            $article->toPrimitives()
        );
    }

    public function remove(ArticleId $id): void
    {
        $this->model->destroy($id->value());
    }

    public function search(ArticleId $id): ?Article
    {
        $model = $this->model->find($id->value());

        return $model ? $this->toDomain($model->toArray()) : null;
    }

    public function searchByCriteria(Criteria $criteria): array
    {
        $eloquentCriteria = EloquentCriteriaConverter::convert($criteria, self::$toEloquentFields);
        $query = $this->model->newQuery();

        $eloquentCriteria->each(static function ($method) use ($query) {
            call_user_func_array([$query, $method->name], $method->parameters);
        });

        $results = $query->get()->toArray();

        return map(fn(array $row) => $this->toDomain($row), $results);
    }

    public function countByCriteria(Criteria $criteria): int
    {
        $eloquentCriteria = EloquentCriteriaConverter::convert($criteria, self::$toEloquentFields);
        $query = $this->model->newQuery();

        $eloquentCriteria->each(static function ($method) use ($query) {
            call_user_func_array([$query, $method->name], $method->parameters);
        });

        return $query->count();
    }

    private function toDomain(array $primitives): Article
    {
        return Article::fromPrimitives($primitives);
    }
}
