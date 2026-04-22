<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Infrastructure\Persistence\Eloquent;

use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filter;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterField;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterOperator;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filters;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterValue;
use Dba\DddSkeleton\Shared\Domain\Criteria\Order;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderBy;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderType;
use Dba\DddSkeleton\Shared\Infrastructure\Persistence\Eloquent\EloquentCriteria;
use Dba\DddSkeleton\Shared\Infrastructure\Persistence\Eloquent\EloquentCriteriaConverter;
use Dba\DddSkeleton\Tests\DbaTestCase;

final class EloquentCriteriaConverterTest extends DbaTestCase
{
    /** @test */
    public function it_should_convert_criteria_to_eloquent_criteria(): void
    {
        $filters = new Filters([
            new Filter(
                new FilterField('name'),
                new FilterOperator(FilterOperator::EQUAL),
                new FilterValue('John')
            ),
        ]);
        $order = new Order(new OrderBy('name'), new OrderType(OrderType::ASC));
        $offset = 10;
        $limit = 20;

        $criteria = new Criteria($filters, $order, $offset, $limit);

        $eloquentCriteria = EloquentCriteriaConverter::convert($criteria);

        $this->assertInstanceOf(EloquentCriteria::class, $eloquentCriteria);

        $methods = $eloquentCriteria->toArray();

        $this->assertCount(4, $methods);

        // Actually, let's check exactly what's in there.
        $methodNames = array_map(fn ($m) => $m['method'], $methods);
        $this->assertContains('where', $methodNames);
        $this->assertContains('orderBy', $methodNames);
        $this->assertContains('offset', $methodNames);
        $this->assertContains('limit', $methodNames);
    }
}
