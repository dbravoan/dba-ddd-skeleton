<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Infrastructure\Persistence\QueryBuilder;

use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filter;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterField;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterOperator;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filters;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterValue;
use Dba\DddSkeleton\Shared\Domain\Criteria\Order;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderBy;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderType;
use Dba\DddSkeleton\Shared\Infrastructure\Persistence\QueryBuilder\QueryBuilderCriteria;
use Dba\DddSkeleton\Shared\Infrastructure\Persistence\QueryBuilder\QueryBuilderCriteriaConverter;
use Dba\DddSkeleton\Tests\DbaTestCase;
use PHPUnit\Framework\Attributes\Test;

final class QueryBuilderCriteriaConverterTest extends DbaTestCase
{
    #[Test]
    public function it_should_convert_criteria_to_query_builder_criteria(): void
    {
        $filters = new Filters([
            new Filter(
                new FilterField('email'),
                FilterOperator::EQUAL,
                new FilterValue('john@example.com')
            ),
        ]);
        $order = new Order(new OrderBy('email'), new OrderType(OrderType::ASC));

        $criteria = new Criteria($filters, $order, 5, 10);
        $qbCriteria = QueryBuilderCriteriaConverter::convert($criteria);

        $this->assertInstanceOf(QueryBuilderCriteria::class, $qbCriteria);

        $methods = $qbCriteria->toArray();
        $methodNames = array_map(fn ($m) => $m['method'], $methods);

        $this->assertContains('where', $methodNames);
        $this->assertContains('orderBy', $methodNames);
        $this->assertContains('offset', $methodNames);
        $this->assertContains('limit', $methodNames);
    }

    #[Test]
    public function it_should_handle_criteria_with_no_filters(): void
    {
        $criteria = new Criteria(new Filters([]), Order::none(), null, null);
        $qbCriteria = QueryBuilderCriteriaConverter::convert($criteria);

        $this->assertInstanceOf(QueryBuilderCriteria::class, $qbCriteria);
        $methods = $qbCriteria->toArray();
        $methodNames = array_map(fn ($m) => $m['method'], $methods);

        $this->assertNotContains('where', $methodNames);
        $this->assertNotContains('offset', $methodNames);
        $this->assertNotContains('limit', $methodNames);
    }

    #[Test]
    public function it_should_apply_field_mapping(): void
    {
        $filters = new Filters([
            new Filter(
                new FilterField('userName'),
                FilterOperator::EQUAL,
                new FilterValue('John')
            ),
        ]);

        $criteria = new Criteria($filters, Order::none(), null, null);
        $qbCriteria = QueryBuilderCriteriaConverter::convert(
            $criteria,
            ['userName' => 'users.name']
        );

        $this->assertInstanceOf(QueryBuilderCriteria::class, $qbCriteria);
        $methods = $qbCriteria->toArray();
        $methodNames = array_map(fn ($m) => $m['method'], $methods);
        $this->assertContains('where', $methodNames);
    }
}
