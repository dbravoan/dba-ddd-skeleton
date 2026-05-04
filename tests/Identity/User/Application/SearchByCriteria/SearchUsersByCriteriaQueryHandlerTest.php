<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Application\SearchByCriteria;

use Dba\DddSkeleton\Identity\User\Application\Response\UsersResponse;
use Dba\DddSkeleton\Identity\User\Application\SearchByCriteria\SearchUsersByCriteriaQuery;
use Dba\DddSkeleton\Identity\User\Application\SearchByCriteria\SearchUsersByCriteriaQueryHandler;
use Dba\DddSkeleton\Identity\User\Domain\User;
use Dba\DddSkeleton\Identity\User\Domain\UserEmail;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserName;
use Dba\DddSkeleton\Identity\User\Domain\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SearchUsersByCriteriaQueryHandlerTest extends TestCase
{
    #[Test]
    public function it_should_return_users_response(): void
    {
        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new UserEmail('john@example.com'),
            new UserName('John Doe')
        );

        $repository = $this->createStub(UserRepository::class);
        $repository->method('searchByCriteria')->willReturn([$user]);

        $handler = new SearchUsersByCriteriaQueryHandler($repository);
        $query = new SearchUsersByCriteriaQuery([], null, null, null, null);
        $result = $handler($query);

        $this->assertInstanceOf(UsersResponse::class, $result);
        $this->assertCount(1, $result->users());
        $this->assertSame('John Doe', $result->users()[0]->name);
    }

    #[Test]
    public function it_should_return_empty_response_when_no_users(): void
    {
        $repository = $this->createStub(UserRepository::class);
        $repository->method('searchByCriteria')->willReturn([]);

        $handler = new SearchUsersByCriteriaQueryHandler($repository);
        $query = new SearchUsersByCriteriaQuery([], null, null, null, null);
        $result = $handler($query);

        $this->assertInstanceOf(UsersResponse::class, $result);
        $this->assertCount(0, $result->users());
    }
}
