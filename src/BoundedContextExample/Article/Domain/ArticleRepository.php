<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Domain;

use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;

interface ArticleRepository
{
    public function save(Article $article): void;
    public function delete(ArticleId $id): void;
    public function search(ArticleId $id): ?Article;
    public function searchByCriteria(Criteria $criteria): array;
    public function countByCriteria(Criteria $criteria): int;
}
