<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\ValueObject;

use Dba\DddSkeleton\Shared\Domain\ValueObject\MoneyValueObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final readonly class TestMoneyValueObject extends MoneyValueObject {}

final class MoneyValueObjectTest extends TestCase
{
    #[Test]
    public function it_should_store_amount_and_currency(): void
    {
        $money = new TestMoneyValueObject(9.99, 'USD');

        $this->assertSame(9.99, $money->amount());
        $this->assertSame('USD', $money->currency());
    }

    #[Test]
    public function it_should_default_to_eur_currency(): void
    {
        $money = new TestMoneyValueObject(10.0);

        $this->assertSame('EUR', $money->currency());
    }

    #[Test]
    public function it_should_be_equal_to_another_with_same_amount_and_currency(): void
    {
        $a = new TestMoneyValueObject(5.0, 'USD');
        $b = new TestMoneyValueObject(5.0, 'USD');

        $this->assertTrue($a->equals($b));
    }

    #[Test]
    public function it_should_not_equal_different_amount(): void
    {
        $a = new TestMoneyValueObject(5.0, 'EUR');
        $b = new TestMoneyValueObject(6.0, 'EUR');

        $this->assertFalse($a->equals($b));
    }

    #[Test]
    public function it_should_not_equal_different_currency(): void
    {
        $a = new TestMoneyValueObject(5.0, 'EUR');
        $b = new TestMoneyValueObject(5.0, 'USD');

        $this->assertFalse($a->equals($b));
    }
}
