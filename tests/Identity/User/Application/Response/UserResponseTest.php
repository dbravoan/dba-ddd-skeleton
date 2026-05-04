<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Application\Response;

use Dba\DddSkeleton\Identity\User\Application\Response\UserResponse;
use Dba\DddSkeleton\Identity\User\Domain\User;
use Dba\DddSkeleton\Identity\User\Domain\UserEmail;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserResponseTest extends TestCase
{
    #[Test]
    public function it_should_build_from_aggregate(): void
    {
        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new UserEmail('john@example.com'),
            new UserName('John Doe')
        );

        $response = UserResponse::fromAggregate($user);

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $response->id);
        $this->assertSame('John Doe', $response->name);
        $this->assertSame('john@example.com', $response->email);
    }

    #[Test]
    public function it_should_convert_to_array(): void
    {
        $response = new UserResponse(
            id: '550e8400-e29b-41d4-a716-446655440000',
            name: 'John Doe',
            email: 'john@example.com'
        );

        $array = $response->toArray();

        $this->assertSame([
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ], $array);
    }
}
