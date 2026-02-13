<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Application\Update;

final class UpdateArticleCommand
{
    public function __construct(
        private readonly string $id,
        private readonly ?string $name,
        private readonly ?float $price,
        private readonly ?int $stock
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function price(): ?float
    {
        return $this->price;
    }

    public function stock(): ?int
    {
        return $this->stock;
    }
}
