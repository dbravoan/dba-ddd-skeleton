<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Update;

use Dba\DddSkeleton\Identity\User\Domain\UserEmail;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandHandler;
use InvalidArgumentException;

final readonly class UpdateUserCommandHandler implements CommandHandler
{
    public function __construct(private UserRepository $repository) {}

    public function __invoke(UpdateUserCommand $command): void
    {
        $userId = new UserId($command->id);
        $user = $this->repository->search($userId);

        if (null === $user) {
            throw new InvalidArgumentException(sprintf('User <%s> does not exist', $command->id));
        }

        // Aquí el agente debe ver que el dominio es el que cambia, no el repo directamente
        $user->rename($command->name);
        $user->changeEmail(new UserEmail($command->email));

        $this->repository->save($user);
    }
}