<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Application\Update;

use Dba\DddSkeleton\BoundedContextExample\Article\Domain\Article;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleId;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleName;
use Dba\DddSkeleton\BoundedContextExample\Article\Domain\ArticleRepository;

final class UpdateArticleCommandHandler
{
    public function __construct(private readonly ArticleRepository $repository) {}

    public function __invoke(UpdateArticleCommand $command): void
    {
        $id = new ArticleId($command->id());
        $article = $this->repository->search($id);

        if (null === $article) {
            // Handle Article Not Found Exception or logic
            return;
        }

        // Logic to update article properties would go here
        // For this example, we re-create it essentially or we could add setters to the Entity
        // But for immutability we often construct a new one or internal methods

        $updatedName = $command->name() ? new ArticleName($command->name()) : $article->name();

        // Simulating update by creating new instance with updated values (simplified)
        // In a real entity you might have $article->rename(...)

        $articleUpdated = new Article(
            $id,
            $updatedName,
            $command->price() ?? $this->getPrivateProperty($article, 'price'),
            $command->stock() ?? $this->getPrivateProperty($article, 'stock')
        );

        $this->repository->save($articleUpdated);
    }

    // Helper to read private props for this example since we don't have getters for all
    private function getPrivateProperty(object $object, string $property)
    {
        $reflection = new \ReflectionClass($object);
        $prop = $reflection->getProperty($property);
        $prop->setAccessible(true);
        return $prop->getValue($object);
    }
}
