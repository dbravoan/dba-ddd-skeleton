<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\ValueObject;

abstract readonly class MoneyValueObject
{
    public function __construct(
        protected float $amount,
        protected string $currency = 'EUR'
    ) {}

    public function amount(): float
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function equals(MoneyValueObject $other): bool
    {
        return $this->amount === $other->amount() && $this->currency === $other->currency();
    }
}
