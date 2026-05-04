<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\Criteria;

use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filter;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterField;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterOperator;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filters;
use Dba\DddSkeleton\Shared\Domain\Criteria\FilterValue;
use Dba\DddSkeleton\Shared\Domain\Criteria\Order;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderBy;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CriteriaTest extends TestCase
{
    #[Test]
    public function it_should_create_criteria(): void
    {
        $filters = new Filters([
            new Filter(
                new FilterField('name'),
                FilterOperator::EQUAL,
                new FilterValue('John')
            ),
        ]);
        $order = new Order(new OrderBy('name'), new OrderType(OrderType::ASC));
        $offset = 0;
        $limit = 10;

        $criteria = new Criteria($filters, $order, $offset, $limit);

        $this->assertEquals($filters, $criteria->filters());
        $this->assertEquals($order, $criteria->order());
        $this->assertEquals($offset, $criteria->offset());
        $this->assertEquals($limit, $criteria->limit());
        $this->assertTrue($criteria->hasFilters());
        $this->assertTrue($criteria->hasOrder());
    }
}
