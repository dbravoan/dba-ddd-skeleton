<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\ValueObject;

use Dba\DddSkeleton\Shared\Domain\Utils;
use ReflectionClass;
use Stringable;

use function in_array;
use function Lambdish\Phunctional\reindex;

/**
 * @phpstan-consistent-constructor
 */
abstract class Enum implements Stringable
{
    /** @var array<string, array<string, mixed>> */
    protected static array $cache = [];

    public function __construct(protected readonly mixed $value)
    {
        $this->ensureIsBetweenAcceptedValues($value);
    }


    abstract protected function throwExceptionForInvalidValue(mixed $value): void;

    /** @param array<mixed> $args */
    public static function __callStatic(string $name, array $args): static
    {
        return new static(self::values()[$name]);
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    /** @return array<string, mixed> */
    public static function values(): array
    {
        $class = static::class;

        if (! isset(self::$cache[$class])) {
            $reflected = new ReflectionClass($class);
            self::$cache[$class] = reindex(self::keysFormatter(), $reflected->getConstants());
        }

        return self::$cache[$class];
    }

    public static function fromNative(\UnitEnum $native): static
    {
        return new static($native instanceof \BackedEnum ? $native->value : $native->name);
    }

    public function toNative(string $nativeEnumClass): \UnitEnum
    {
        if (is_subclass_of($nativeEnumClass, \BackedEnum::class)) {
            /** @var int|string $val */
            $val = $this->value;

            return $nativeEnumClass::from($val);
        }

        if (! is_string($this->value)) {
            throw new \InvalidArgumentException('Value must be a string to convert to a non-backed Enum');
        }

        /** @var \UnitEnum $constant */
        $constant = constant($nativeEnumClass.'::'.$this->value);

        return $constant;
    }

    public static function randomValue(): mixed
    {
        return self::values()[array_rand(self::values())];
    }

    public static function random(): static
    {
        return new static(self::randomValue());
    }

    private static function keysFormatter(): callable
    {
        return static fn ($unused, string $key): string => Utils::toCamelCase(strtolower($key));
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function equals(Enum $other): bool
    {
        return $other->value() === $this->value();
    }

    public function __toString(): string
    {
        if (is_scalar($this->value)) {
            return (string) $this->value;
        }

        return '';
    }

    private function ensureIsBetweenAcceptedValues(mixed $value): void
    {
        if (! in_array($value, static::values(), true)) {
            $this->throwExceptionForInvalidValue($value);
        }
    }

    public static function from(mixed $value): static
    {
        return new static($value);
    }
}
