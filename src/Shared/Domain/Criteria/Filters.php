<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Criteria;

final class Filters
{
    /** @param Filter[] $filters */
    public function __construct(private readonly array $filters) {}

    public static function fromValues(array $values): self
    {
        return new self(array_map(fn($filter) => Filter::fromValues($filter), $values));
    }

    public function filters(): array
    {
        return $this->filters;
    }

    public function add(Filter $filter): self
    {
        return new self(array_merge($this->filters, [$filter]));
    }
}
