<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Criteria;

final class FilterValue
{
    public function __construct(private readonly mixed $value) {}

    public function value(): mixed
    {
        return $this->value;
    }
}
