<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Create;

use Dba\DddSkeleton\Identity\User\Domain\User;
use Dba\DddSkeleton\Identity\User\Domain\UserEmail;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandHandler;

final readonly class CreateUserCommandHandler implements CommandHandler
{
    public function __construct(private UserRepository $repository) {}

    public function __invoke(CreateUserCommand $command): void
    {
        $user = User::create(
            new UserId($command->id),
            new UserEmail($command->email),
            $command->name
        );

        $this->repository->save($user);
    }
}