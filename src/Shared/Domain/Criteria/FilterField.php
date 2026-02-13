<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Criteria;

final class FilterField
{
    public function __construct(private readonly string $value) {}

    public function value(): string
    {
        return $this->value;
    }
}
