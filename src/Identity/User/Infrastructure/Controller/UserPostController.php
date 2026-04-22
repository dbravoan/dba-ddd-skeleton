<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Infrastructure\Controller;

use Dba\DddSkeleton\Identity\User\Application\Create\CreateUserCommand;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandBus;
use Dba\DddSkeleton\Shared\Infrastructure\Laravel\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserPostController extends ApiController
{
    public function __construct(private CommandBus $bus) {}

    public function __invoke(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $email = $request->input('email');

        $command = new CreateUserCommand(
            is_string($id) ? $id : '',
            is_string($name) ? $name : '',
            is_string($email) ? $email : ''
        );

        $this->bus->dispatch($command);

        return $this->sendResponse(null, 'User created successfully');
    }
}
