<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\ValueObject;

abstract class IntValueObject
{
    public function __construct(protected int $value) {}

    public function value(): int
    {
        return $this->value;
    }

    public function isBiggerThan(IntValueObject $other): bool
    {
        return $this->value() > $other->value();
    }

    public static function from(int $value): self
    {
        return new static($value);
    }
}
