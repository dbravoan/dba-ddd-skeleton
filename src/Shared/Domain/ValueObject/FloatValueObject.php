<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\ValueObject;

/**
 * @phpstan-consistent-constructor
 */
abstract readonly class FloatValueObject
{
    public function __construct(protected float $value) {}

    public function value(): float
    {
        return $this->value;
    }

    public function isBiggerThan(FloatValueObject $other): bool
    {
        return $this->value() > $other->value();
    }

    public function isSmallerThan(FloatValueObject $other): bool
    {
        return $this->value() < $other->value();
    }

    public static function from(float $value): self
    {
        return new static($value);
    }
}
