<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Infrastructure\Persistence;

use Dba\DddSkeleton\BoundedContextExample\Article\Domain\Article;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleId;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleRepository;
use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Illuminate\Database\DatabaseManager;

use function Lambdish\Phunctional\map;

final class QueryBuilderArticleRepository implements ArticleRepository
{
    private static array $toQueryBuilderFields = [
        'id' => 'article_id',
        'name' => 'article_name',
        'price' => 'article_price',
        'stock' => 'article_stock',
    ];

    public function __construct(private readonly DatabaseManager $db) {}

    public function save(Article $article): void
    {
        $data = $article->toPrimitives();
        // Mapping primitives back to DB columns
        $dbData = [
            'article_id' => $data['id'],
            'article_name' => $data['name'],
            'article_price' => $data['price'],
            'article_stock' => $data['stock'],
        ];

        $this->db->table('articles')->updateOrInsert(
            ['article_id' => $data['id']],
            $dbData
        );
    }

    public function delete(ArticleId $id): void
    {
        $this->db->table('articles')->where('article_id', $id->value())->delete();
    }

    public function search(ArticleId $id): ?Article
    {
        $row = $this->db->table('articles')->where('article_id', $id->value())->first();

        return $row ? $this->toDomain((array) $row) : null;
    }

    public function searchByCriteria(Criteria $criteria): array
    {
        $query = $this->db->table('articles');

        // Apply criteria logic
        // ... (Implementation detail similar to Eloquent but using Query Builder)

        $results = $query->get()->map(fn($item) => (array) $item)->toArray();

        return map(fn(array $row) => $this->toDomain($row), $results);
    }

    public function countByCriteria(Criteria $criteria): int
    {
        $query = $this->db->table('articles');
        // Apply criteria logic
        return $query->count();
    }

    private function toDomain(array $primitives): Article
    {
        // Mapping DB columns to Domain fields
        return Article::fromPrimitives([
            'id' => $primitives['article_id'],
            'name' => $primitives['article_name'],
            'price' => $primitives['article_price'],
            'stock' => $primitives['article_stock'],
        ]);
    }
}
