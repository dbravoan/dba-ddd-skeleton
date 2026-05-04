<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Infrastructure\Criteria;

use Dba\DddSkeleton\Shared\Infrastructure\Criteria\RequestCriteriaBuilder;
use Dba\DddSkeleton\Tests\DbaTestCase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;

final class RequestCriteriaBuilderTest extends DbaTestCase
{
    #[Test]
    public function it_should_build_empty_criteria_from_empty_request(): void
    {
        $request = Request::create('/');
        $criteria = (new RequestCriteriaBuilder)->build($request);

        $this->assertFalse($criteria->hasFilters());
    }

    #[Test]
    public function it_should_build_criteria_with_limit_and_offset(): void
    {
        $request = Request::create('/', 'GET', ['limit' => '15', 'offset' => '5']);
        $criteria = (new RequestCriteriaBuilder)->build($request);

        $this->assertSame(15, $criteria->limit());
        $this->assertSame(5, $criteria->offset());
    }

    #[Test]
    public function it_should_build_criteria_with_order(): void
    {
        $request = Request::create('/', 'GET', ['order_by' => 'name', 'order' => 'DESC']);
        $criteria = (new RequestCriteriaBuilder)->build($request);

        $this->assertTrue($criteria->hasOrder());
        $this->assertSame('name', $criteria->order()->orderBy()->value());
        $this->assertSame('DESC', $criteria->order()->orderType()->value());
    }

    #[Test]
    public function it_should_build_criteria_with_filters(): void
    {
        // The builder expects 'filters' to already be an array in the request
        // (decoded by middleware or passed as array params).
        $request = Request::create('/', 'GET');
        $request->merge([
            'filters' => [
                [
                    'conditions' => [
                        ['field' => 'name', 'operator' => '=', 'value' => 'John'],
                    ],
                ],
            ],
        ]);

        $criteria = (new RequestCriteriaBuilder)->build($request);

        $this->assertTrue($criteria->hasFilters());
    }

    #[Test]
    public function it_should_use_defaults_when_no_limit_or_offset_given(): void
    {
        $request = Request::create('/');
        $criteria = (new RequestCriteriaBuilder)->build($request);

        $this->assertNull($criteria->limit());
        $this->assertNull($criteria->offset());
    }

    #[Test]
    public function it_should_handle_invalid_json_filters_gracefully(): void
    {
        $request = Request::create('/', 'GET', ['filters' => 'not-json']);
        $criteria = (new RequestCriteriaBuilder)->build($request);

        $this->assertFalse($criteria->hasFilters());
    }
}
