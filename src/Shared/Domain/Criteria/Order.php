<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Criteria;

final class Order
{
    public function __construct(
        private readonly OrderBy $orderBy,
        private readonly OrderType $orderType
    ) {}

    public static function createDesc(OrderBy $orderBy): self
    {
        return new self($orderBy, OrderType::desc());
    }

    public static function createAsc(OrderBy $orderBy): self
    {
        return new self($orderBy, OrderType::asc());
    }

    public static function none(): self
    {
        return new self(new OrderBy(''), OrderType::none());
    }

    public function orderBy(): OrderBy
    {
        return $this->orderBy;
    }

    public function orderType(): OrderType
    {
        return $this->orderType;
    }

    public function isNone(): bool
    {
        return $this->orderType->isNone();
    }
}
