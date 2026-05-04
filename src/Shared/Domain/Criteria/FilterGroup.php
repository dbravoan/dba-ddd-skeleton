<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Criteria;

final class FilterGroup
{
    /**
     * @param  array<int, Filter>  $filters
     */
    public function __construct(
        private readonly array $filters,
        private readonly string $glue
    ) {}

    /**
     * @param  array<int, Filter>  $filters
     */
    public static function fromValues(array $filters, string $glue): self
    {
        return new self($filters, $glue);
    }

    /**
     * @return array<int, Filter>
     */
    public function filters(): array
    {
        return $this->filters;
    }

    public function glue(): string
    {
        return $this->glue;
    }

    public function serialize(): string
    {
        $serialized_filters = array_map(fn ($filter) => $filter->serialize(), $this->filters);

        return sprintf('(%s)', implode(' '.$this->glue.' ', $serialized_filters));
    }
}
