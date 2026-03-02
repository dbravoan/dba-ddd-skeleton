<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Bus\Query;

final class CountResponse implements Response
{
    public function __construct(private readonly int $count) {}

    public function count(): int
    {
        return $this->count;
    }
}
