<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Infrastructure\Controller;

use Dba\DddSkeleton\Identity\User\Application\SearchByCriteria\SearchUsersByCriteriaQuery;
use Dba\DddSkeleton\Identity\User\Application\Response\UsersResponse;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\QueryBus;
use Dba\DddSkeleton\Shared\Infrastructure\Criteria\RequestCriteriaBuilder;
use Dba\DddSkeleton\Shared\Infrastructure\Laravel\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UsersGetController extends ApiController
{
    public function __construct(
        private readonly QueryBus $bus,
        private readonly RequestCriteriaBuilder $criteriaBuilder
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $criteria = $this->criteriaBuilder->buildFromRequest($request);

        $query = new SearchUsersByCriteriaQuery(
            $request->get('filters', []),
            $criteria->order()->orderBy()?->value(),
            $criteria->order()->orderType()?->value(),
            $criteria->limit(),
            (int) $criteria->offset()
        );

        /** @var UsersResponse $response */
        $response = $this->bus->ask($query);

        // En un entorno real, aquí también llamaríamos a un CountQuery para la meta-información
        return $this->sendResponse([
            'data' => $response->toArray(),
            'meta' => [
                'total'  => count($response->users()),
                'limit'  => $criteria->limit(),
                'offset' => $criteria->offset(),
            ]
        ], 'Users searched successfully');
    }
}