<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Infrastructure\Controller;

use Dba\DddSkeleton\Identity\User\Application\Update\UpdateUserCommand;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandBus;
use Dba\DddSkeleton\Shared\Infrastructure\Laravel\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserPutController extends ApiController
{
    public function __construct(private readonly CommandBus $bus) {}

    public function __invoke(string $id, Request $request): JsonResponse
    {
        $command = new UpdateUserCommand(
            $id,
            $request->input('name'),
            $request->input('email')
        );

        $this->bus->dispatch($command);

        return $this->sendResponse(null, 'User updated successfully');
    }
}