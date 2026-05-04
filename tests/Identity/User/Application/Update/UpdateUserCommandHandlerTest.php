<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Application\Update;

use Dba\DddSkeleton\Identity\User\Application\Update\UpdateUserCommand;
use Dba\DddSkeleton\Identity\User\Application\Update\UpdateUserCommandHandler;
use Dba\DddSkeleton\Identity\User\Domain\User;
use Dba\DddSkeleton\Identity\User\Domain\UserEmail;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserName;
use Dba\DddSkeleton\Identity\User\Domain\UserNotFoundDomainError;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UpdateUserCommandHandlerTest extends TestCase
{
    #[Test]
    public function it_should_update_and_save_the_user(): void
    {
        $id = '550e8400-e29b-41d4-a716-446655440000';
        $user = new User(
            new UserId($id),
            new UserEmail('old@example.com'),
            new UserName('Old Name')
        );

        $repository = $this->createMock(UserRepository::class);
        $repository->method('search')->willReturn($user);
        $repository->expects($this->once())->method('save')->with($user);

        $handler = new UpdateUserCommandHandler($repository);
        $handler(new UpdateUserCommand($id, 'New Name', 'new@example.com'));

        $this->assertSame('New Name', $user->name()->value());
        $this->assertSame('new@example.com', $user->email()->value());
    }

    #[Test]
    public function it_should_throw_when_user_not_found(): void
    {
        $repository = $this->createStub(UserRepository::class);
        $repository->method('search')->willReturn(null);

        $handler = new UpdateUserCommandHandler($repository);

        $this->expectException(UserNotFoundDomainError::class);

        $handler(new UpdateUserCommand('550e8400-e29b-41d4-a716-446655440000', 'Name', 'email@example.com'));
    }
}
