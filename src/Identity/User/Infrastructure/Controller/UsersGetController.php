<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Infrastructure\Controller;

use Dba\DddSkeleton\Identity\User\Application\Response\UsersResponse;
use Dba\DddSkeleton\Identity\User\Application\SearchByCriteria\SearchUsersByCriteriaQuery;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\QueryBus;
use Dba\DddSkeleton\Shared\Infrastructure\Laravel\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UsersGetController extends ApiController
{
    public function __construct(private QueryBus $bus) {}

    public function __invoke(Request $request): JsonResponse
    {
        /** @var array<int, array<string, mixed>> $filters */
        $filters = $request->get('filters', []);
        $orderBy = $request->get('order_by');
        $orderType = $request->get('order');
        $limit = $request->get('limit');
        $offset = $request->get('offset');

        $query = new SearchUsersByCriteriaQuery(
            $filters,
            is_string($orderBy) ? $orderBy : null,
            is_string($orderType) ? $orderType : null,
            is_numeric($limit) ? (int) $limit : null,
            is_numeric($offset) ? (int) $offset : null
        );

        /** @var UsersResponse|null $response */
        $response = $this->bus->ask($query);

        return $this->sendResponse($response?->toArray(), 'Users retrieved successfully');
    }
}
