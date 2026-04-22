<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\ValueObject;

/**
 * @phpstan-consistent-constructor
 */
abstract readonly class StringValueObject
{
    public function __construct(protected string $value) {}

    public function value(): string
    {
        return $this->value;
    }

    public static function from(string $value): self
    {
        return new static($value);
    }
}
