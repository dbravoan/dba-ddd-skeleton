<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Application\Delete;

use Dba\DddSkeleton\Identity\User\Application\Delete\DeleteUserCommand;
use Dba\DddSkeleton\Identity\User\Application\Delete\DeleteUserCommandHandler;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteUserCommandHandlerTest extends TestCase
{
    /** @var UserRepository&MockObject */
    private UserRepository $repository;

    private DeleteUserCommandHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->handler = new DeleteUserCommandHandler($this->repository);
    }

    #[Test]
    public function it_should_delete_the_user_by_id(): void
    {
        $id = '550e8400-e29b-41d4-a716-446655440000';

        $this->repository
            ->expects($this->once())
            ->method('delete')
            ->with($this->callback(fn (UserId $userId) => $userId->value() === $id));

        ($this->handler)(new DeleteUserCommand($id));
    }
}
