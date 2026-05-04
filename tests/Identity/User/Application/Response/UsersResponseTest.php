<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Application\Response;

use Dba\DddSkeleton\Identity\User\Application\Response\UserResponse;
use Dba\DddSkeleton\Identity\User\Application\Response\UsersResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UsersResponseTest extends TestCase
{
    #[Test]
    public function it_should_return_users(): void
    {
        $userA = new UserResponse('id-1', 'Alice', 'alice@example.com');
        $userB = new UserResponse('id-2', 'Bob', 'bob@example.com');

        $response = new UsersResponse([$userA, $userB]);

        $this->assertCount(2, $response->users());
        $this->assertSame($userA, $response->users()[0]);
        $this->assertSame($userB, $response->users()[1]);
    }

    #[Test]
    public function it_should_convert_to_array(): void
    {
        $response = new UsersResponse([
            new UserResponse('id-1', 'Alice', 'alice@example.com'),
        ]);

        $array = $response->toArray();

        $this->assertSame([
            ['id' => 'id-1', 'name' => 'Alice', 'email' => 'alice@example.com'],
        ], $array);
    }

    #[Test]
    public function it_should_return_empty_array_when_no_users(): void
    {
        $response = new UsersResponse([]);

        $this->assertSame([], $response->toArray());
        $this->assertSame([], $response->users());
    }
}
