<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\ValueObject;

use Dba\DddSkeleton\Shared\Domain\ValueObject\BoolValueObject;
use Dba\DddSkeleton\Shared\Domain\ValueObject\FloatValueObject;
use Dba\DddSkeleton\Shared\Domain\ValueObject\IntValueObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ValueObjectTest extends TestCase
{
    #[Test]
    public function it_should_store_a_bool_value(): void
    {
        $trueValue = new StubBoolValueObject(true);
        $falseValue = new StubBoolValueObject(false);

        $this->assertTrue($trueValue->value());
        $this->assertTrue($trueValue->isTrue());
        $this->assertFalse($trueValue->isFalse());

        $this->assertFalse($falseValue->value());
        $this->assertFalse($falseValue->isTrue());
        $this->assertTrue($falseValue->isFalse());
    }

    #[Test]
    public function it_should_store_an_int_value(): void
    {
        $value1 = new StubIntValueObject(10);
        $value2 = new StubIntValueObject(5);

        $this->assertEquals(10, $value1->value());
        $this->assertTrue($value1->isBiggerThan($value2));
        $this->assertFalse($value2->isBiggerThan($value1));
    }

    #[Test]
    public function it_should_store_a_float_value(): void
    {
        $value1 = new StubFloatValueObject(10.5);
        $value2 = new StubFloatValueObject(5.5);

        $this->assertEquals(10.5, $value1->value());
        $this->assertTrue($value1->isBiggerThan($value2));
        $this->assertTrue($value2->isSmallerThan($value1));
    }
}

final readonly class StubBoolValueObject extends BoolValueObject {}

final readonly class StubIntValueObject extends IntValueObject {}

final readonly class StubFloatValueObject extends FloatValueObject {}
