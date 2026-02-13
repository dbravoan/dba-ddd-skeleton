<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Domain;

final class Article
{
    public function __construct(
        private readonly ArticleId $id,
        private readonly ArticleName $name,
        private readonly float $price,
        private readonly int $stock
    ) {}

    public static function create(ArticleId $id, ArticleName $name, float $price, int $stock): self
    {
        return new self($id, $name, $price, $stock);
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self(
            new ArticleId($primitives['id']),
            new ArticleName($primitives['name']),
            (float) $primitives['price'],
            (int) $primitives['stock']
        );
    }

    public function id(): ArticleId
    {
        return $this->id;
    }

    public function name(): ArticleName
    {
        return $this->name;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function stock(): int
    {
        return $this->stock;
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'price' => $this->price,
            'stock' => $this->stock,
        ];
    }
}
