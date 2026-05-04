<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Update;

use Dba\DddSkeleton\Identity\User\Domain\UserEmail;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserName;
use Dba\DddSkeleton\Identity\User\Domain\UserNotFoundDomainError;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use Dba\DddSkeleton\Shared\Domain\Bus\Command\CommandHandler;

final readonly class UpdateUserCommandHandler implements CommandHandler
{
    public function __construct(private UserRepository $repository) {}

    public function __invoke(UpdateUserCommand $command): void
    {
        $userId = new UserId($command->id);
        $user = $this->repository->search($userId);

        if ($user === null) {
            throw new UserNotFoundDomainError($command->id);
        }

        $user->rename(new UserName($command->name));
        $user->changeEmail(new UserEmail($command->email));

        $this->repository->save($user);
    }
}
