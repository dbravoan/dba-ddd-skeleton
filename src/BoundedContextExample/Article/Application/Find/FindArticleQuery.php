<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\BoundedContextExample\Article\Application\Find;

final class FindArticleQuery
{
    public function __construct(private readonly string $id) {}

    public function id(): string
    {
        return $this->id;
    }
}
