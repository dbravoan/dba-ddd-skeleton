<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\ValueObject;

use Dba\DddSkeleton\Shared\Domain\ValueObject\BoolValueObject;
use Dba\DddSkeleton\Shared\Domain\ValueObject\FloatValueObject;
use Dba\DddSkeleton\Shared\Domain\ValueObject\IntValueObject;
use PHPUnit\Framework\TestCase;

final class ValueObjectTest extends TestCase
{
    /** @test */
    public function it_should_store_a_bool_value(): void
    {
        $trueValue = new readonly class(true) extends BoolValueObject {};
        $falseValue = new readonly class(false) extends BoolValueObject {};

        $this->assertTrue($trueValue->value());
        $this->assertTrue($trueValue->isTrue());
        $this->assertFalse($trueValue->isFalse());

        $this->assertFalse($falseValue->value());
        $this->assertFalse($falseValue->isTrue());
        $this->assertTrue($falseValue->isFalse());
    }

    /** @test */
    public function it_should_store_an_int_value(): void
    {
        $value1 = new readonly class(10) extends IntValueObject {};
        $value2 = new readonly class(5) extends IntValueObject {};

        $this->assertEquals(10, $value1->value());
        $this->assertTrue($value1->isBiggerThan($value2));
        $this->assertFalse($value2->isBiggerThan($value1));
    }

    /** @test */
    public function it_should_store_a_float_value(): void
    {
        $value1 = new readonly class(10.5) extends FloatValueObject {};
        $value2 = new readonly class(5.5) extends FloatValueObject {};

        $this->assertEquals(10.5, $value1->value());
        $this->assertTrue($value1->isBiggerThan($value2));
        $this->assertTrue($value2->isSmallerThan($value1));
    }
}
