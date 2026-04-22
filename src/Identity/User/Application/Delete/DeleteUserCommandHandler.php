<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Delete;

use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandHandler;

final readonly class DeleteUserCommandHandler implements CommandHandler
{
    public function __construct(private UserRepository $repository) {}

    public function __invoke(DeleteUserCommand $command): void
    {
        $userId = new UserId($command->id());

        $this->repository->delete($userId);
    }
}
