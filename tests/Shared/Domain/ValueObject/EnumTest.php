<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\ValueObject;

use Dba\DddSkeleton\Shared\Domain\ValueObject\Enum;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EnumTest extends TestCase
{
    #[Test]
    public function it_should_create_an_enum_from_value(): void
    {
        $enum = StubEnum::from(StubEnum::VALUE_1);
        $this->assertEquals(StubEnum::VALUE_1, $enum->value());
    }

    #[Test]
    public function it_should_throw_exception_for_invalid_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        StubEnum::from('invalid-value');
    }

    #[Test]
    public function it_should_allow_creation_via_static_call(): void
    {
        $enum = StubEnum::value1();
        $this->assertInstanceOf(StubEnum::class, $enum);
        $this->assertEquals(StubEnum::VALUE_1, $enum->value());
    }

    #[Test]
    public function it_should_get_all_values(): void
    {
        $values = StubEnum::values();
        $this->assertEquals([
            'value1' => StubEnum::VALUE_1,
            'value2' => StubEnum::VALUE_2,
        ], $values);
    }
}

/**
 * @method static StubEnum value1()
 * @method static StubEnum value2()
 */
final class StubEnum extends Enum
{
    public const VALUE_1 = 'value_1';

    public const VALUE_2 = 'value_2';

    protected function throwExceptionForInvalidValue($value): void
    {
        throw new InvalidArgumentException("Invalid value: $value");
    }
}
