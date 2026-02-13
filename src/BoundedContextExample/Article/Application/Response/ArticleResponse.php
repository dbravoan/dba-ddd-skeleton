<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Application\Response;

use Dba\DddSkeleton\BoundedContextExample\Article\Domain\Article;

final class ArticleResponse
{
    private string $id;
    private string $name;
    private float $price;
    private int $stock;

    public function __construct(string $id, string $name, float $price, int $stock)
    {
        $this->id    = $id;
        $this->name  = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    public static function fromAggregate(Article $article): self
    {
        return new self(
            $article->id()->value(),
            $article->name()->value(),
            $article->price(),
            $article->stock()
        );
    }

    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
        ];
    }
}
