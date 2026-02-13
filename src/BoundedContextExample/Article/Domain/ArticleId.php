<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Domain;

final class ArticleId
{
    public function __construct(private readonly string $value) {}

    public function value(): string
    {
        return $this->value;
    }
}
