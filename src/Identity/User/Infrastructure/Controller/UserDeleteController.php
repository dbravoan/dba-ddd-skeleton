<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Infrastructure\Controller;

use Dba\DddSkeleton\Identity\User\Application\Delete\DeleteUserCommand;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandBus;
use Dba\DddSkeleton\Shared\Infrastructure\Laravel\ApiController;
use Illuminate\Http\JsonResponse;

final class UserDeleteController extends ApiController
{
    public function __construct(private readonly CommandBus $bus) {}

    public function __invoke(string $id): JsonResponse
    {
        $this->bus->dispatch(new DeleteUserCommand($id));

        return $this->sendResponse(null, 'User deleted successfully');
    }
}