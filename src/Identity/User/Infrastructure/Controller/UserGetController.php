<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Infrastructure\Controller;

use Dba\DddSkeleton\Identity\User\Application\Find\FindUserQuery;
use Dba\DddSkeleton\Identity\User\Application\Response\UserResponse;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\QueryBus;
use Dba\DddSkeleton\Shared\Infrastructure\Laravel\ApiController;
use Illuminate\Http\JsonResponse;

final class UserGetController extends ApiController
{
    public function __construct(private readonly QueryBus $bus) {}

    public function __invoke(string $id): JsonResponse
    {
        /** @var UserResponse|null $response */
        $response = $this->bus->ask(new FindUserQuery($id));

        if (null === $response) {
            return $this->sendError('User not found', [], 404);
        }

        return $this->sendResponse($response->toArray(), 'User retrieved successfully');
    }
}