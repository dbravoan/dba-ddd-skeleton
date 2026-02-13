<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Criteria;

final class OrderType
{
    public const ASC = 'asc';
    public const DESC = 'desc';
    public const NONE = 'none';

    public function __construct(private readonly string $value) {}

    public static function desc(): self
    {
        return new self(self::DESC);
    }

    public static function asc(): self
    {
        return new self(self::ASC);
    }

    public static function none(): self
    {
        return new self(self::NONE);
    }

    public function isNone(): bool
    {
        return $this->value === self::NONE;
    }

    public function value(): string
    {
        return $this->value;
    }
}
