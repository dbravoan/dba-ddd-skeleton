<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Application\Find;

use Dba\DddSkeleton\Identity\User\Application\Find\FindUserQuery;
use Dba\DddSkeleton\Identity\User\Application\Find\FindUserQueryHandler;
use Dba\DddSkeleton\Identity\User\Application\Response\UserResponse;
use Dba\DddSkeleton\Identity\User\Domain\User;
use Dba\DddSkeleton\Identity\User\Domain\UserEmail;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserName;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FindUserQueryHandlerTest extends TestCase
{
    #[Test]
    public function it_should_return_a_user_response_when_found(): void
    {
        $id = '550e8400-e29b-41d4-a716-446655440000';
        $user = new User(
            new UserId($id),
            new UserEmail('john@example.com'),
            new UserName('John Doe')
        );

        $repository = $this->createStub(UserRepository::class);
        $repository->method('search')->willReturn($user);

        $handler = new FindUserQueryHandler($repository);
        $result = $handler(new FindUserQuery($id));

        $this->assertInstanceOf(UserResponse::class, $result);
        $this->assertSame($id, $result->id);
        $this->assertSame('John Doe', $result->name);
        $this->assertSame('john@example.com', $result->email);
    }

    #[Test]
    public function it_should_return_null_when_user_not_found(): void
    {
        $repository = $this->createStub(UserRepository::class);
        $repository->method('search')->willReturn(null);

        $handler = new FindUserQueryHandler($repository);
        $result = $handler(new FindUserQuery('550e8400-e29b-41d4-a716-446655440000'));

        $this->assertNull($result);
    }
}
