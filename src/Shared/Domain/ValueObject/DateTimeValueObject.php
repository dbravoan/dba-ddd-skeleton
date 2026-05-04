<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\ValueObject;

use DateTimeImmutable;
use Dba\DddSkeleton\Shared\Domain\Utils;

/**
 * @phpstan-consistent-constructor
 */
abstract readonly class DateTimeValueObject
{
    public function __construct(protected string $value) {}

    public function value(): string
    {
        return $this->value;
    }

    public static function now(): static
    {
        return new static(Utils::dateToString(new DateTimeImmutable));
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
