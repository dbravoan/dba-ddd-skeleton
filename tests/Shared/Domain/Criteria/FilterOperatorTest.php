<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\Criteria;

use Dba\DddSkeleton\Shared\Domain\Criteria\FilterOperator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

final class FilterOperatorTest extends TestCase
{
    #[Test]
    public function it_should_create_from_valid_value(): void
    {
        $operator = FilterOperator::from('=');

        $this->assertSame(FilterOperator::EQUAL, $operator);
        $this->assertSame('=', $operator->value());
    }

    #[Test]
    public function it_should_throw_for_invalid_value(): void
    {
        $this->expectException(ValueError::class);

        FilterOperator::from('INVALID_OPERATOR');
    }

    #[Test]
    public function it_should_have_all_expected_cases(): void
    {
        $cases = FilterOperator::cases();
        $values = array_map(fn (FilterOperator $op) => $op->value, $cases);

        $this->assertContains('=', $values);
        $this->assertContains('!=', $values);
        $this->assertContains('IN', $values);
        $this->assertContains('NOT IN', $values);
        $this->assertContains('CONTAINS', $values);
        $this->assertContains('BETWEEN', $values);
        $this->assertContains('IS_NULL', $values);
        $this->assertContains('IS_NOT_NULL', $values);
    }

    #[Test]
    public function it_should_return_value_via_method(): void
    {
        $this->assertSame('CONTAINS', FilterOperator::CONTAINS->value());
        $this->assertSame('IS_NULL', FilterOperator::IS_NULL->value());
    }
}
