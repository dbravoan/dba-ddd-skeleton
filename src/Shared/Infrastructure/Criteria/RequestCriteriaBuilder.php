<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Criteria;

use Dba\DddSkeleton\Shared\Domain\Criteria\Criteria;
use Dba\DddSkeleton\Shared\Domain\Criteria\Filters;
use Dba\DddSkeleton\Shared\Domain\Criteria\Order;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderBy;
use Dba\DddSkeleton\Shared\Domain\Criteria\OrderType;
use Illuminate\Http\Request;

final class RequestCriteriaBuilder
{
    public function buildFromRequest(Request $request): Criteria
    {
        $filters = $request->get('filters', []);
        $orderBy = $request->get('order_by');
        $orderType = $request->get('order_type');
        $limit = $request->get('limit') ? (int) $request->get('limit') : null;
        $offset = $request->get('offset') ? (int) $request->get('offset') : null;
        $glue = $request->get('glue', 'AND');

        $criteriaFilters = Filters::fromValues(is_array($filters) ? $filters : []);

        if ($orderBy) {
            $criteriaOrder = new Order(
                new OrderBy($orderBy),
                new OrderType($orderType ?? 'asc')
            );
        } else {
            $criteriaOrder = Order::none();
        }

        return new Criteria($criteriaFilters, $criteriaOrder, $offset, $limit, $glue);
    }
}
