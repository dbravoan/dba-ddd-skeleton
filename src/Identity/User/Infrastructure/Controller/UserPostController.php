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
    public function __construct(
        private readonly CommandBus $bus
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        // 1. Validar la request (opcionalmente usar un FormRequest)
        $request->validate([
            'id'    => 'required|uuid',
            'name'  => 'required|string',
            'email' => 'required|email',
        ]);

        // 2. Crear el comando con los datos de la petición
        $command = new CreateUserCommand(
            $request->input('id'),
            $request->input('name'),
            $request->input('email')
        );

        // 3. Despachar al Bus de Comandos
        // El bus localizará el CreateUserCommandHandler automáticamente
        $this->bus->dispatch($command);

        // 4. Devolver respuesta de éxito usando el helper del skeleton
        return $this->sendResponse(null, 'User created successfully');
    }
}