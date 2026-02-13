<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\ValueObject;

abstract class BoolValueObject
{
    public function __construct(protected bool $value) {}

    public function value(): bool
    {
        return $this->value;
    }

    public function isTrue(): bool
    {
        return $this->value();
    }

    public function isFalse(): bool
    {
        return !$this->value();
    }

    public static function from(bool $value): self
    {
        return new static($value);
    }
}
