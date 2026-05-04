<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\ValueObject;

use Dba\DddSkeleton\Shared\Domain\ValueObject\DateTimeValueObject;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final readonly class TestDateTimeValueObject extends DateTimeValueObject {}

final class DateTimeValueObjectTest extends TestCase
{
    #[Test]
    public function it_should_store_a_date_string(): void
    {
        $dt = new TestDateTimeValueObject('2024-01-15T10:30:00+00:00');

        $this->assertSame('2024-01-15T10:30:00+00:00', $dt->value());
    }

    #[Test]
    public function it_should_cast_to_string(): void
    {
        $dt = new TestDateTimeValueObject('2024-01-15T10:30:00+00:00');

        $this->assertSame('2024-01-15T10:30:00+00:00', (string) $dt);
    }

    #[Test]
    public function it_should_create_from_string(): void
    {
        $dt = TestDateTimeValueObject::fromString('2024-06-01T00:00:00+00:00');

        $this->assertSame('2024-06-01T00:00:00+00:00', $dt->value());
    }

    #[Test]
    public function it_should_create_now(): void
    {
        $before = time();
        $dt = TestDateTimeValueObject::now();
        $after = time();

        $timestamp = strtotime($dt->value());

        $this->assertGreaterThanOrEqual($before, $timestamp);
        $this->assertLessThanOrEqual($after, $timestamp);
    }
}
