<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\Criteria;

use Dba\DddSkeleton\Shared\Domain\Criteria\Filter;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterField;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterGroup;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterOperator;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterValue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FilterGroupTest extends TestCase
{
    #[Test]
    public function it_should_create_a_filter_group_with_filters(): void
    {
        $filter = new Filter(new FilterField('name'), FilterOperator::EQUAL, new FilterValue('John'));
        $group = new FilterGroup([$filter], 'AND');

        $this->assertCount(1, $group->filters());
    }

    #[Test]
    public function it_should_allow_multiple_filters(): void
    {
        $filter1 = new Filter(new FilterField('name'), FilterOperator::EQUAL, new FilterValue('John'));
        $filter2 = new Filter(new FilterField('email'), FilterOperator::CONTAINS, new FilterValue('@example'));
        $group = new FilterGroup([$filter1, $filter2], 'AND');

        $this->assertCount(2, $group->filters());
    }

    #[Test]
    public function it_should_store_the_glue(): void
    {
        $filter = new Filter(new FilterField('status'), FilterOperator::EQUAL, new FilterValue('active'));
        $group = new FilterGroup([$filter], 'OR');

        $this->assertSame('OR', $group->glue());
    }

    #[Test]
    public function it_should_serialize_to_string(): void
    {
        $filter = new Filter(new FilterField('name'), FilterOperator::EQUAL, new FilterValue('John'));
        $group = new FilterGroup([$filter], 'AND');

        $serialized = $group->serialize();

        $this->assertIsString($serialized);
        $this->assertStringContainsString('name', $serialized);
    }
}
