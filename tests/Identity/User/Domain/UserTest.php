<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Domain;

use Dba\DddSkeleton\Identity\User\Domain\User;
use Dba\DddSkeleton\Identity\User\Domain\UserCreatedDomainEvent;
use Dba\DddSkeleton\Identity\User\Domain\UserEmail;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    /** @test */
    public function it_should_create_a_user(): void
    {
        $id = UserId::random();
        $email = new UserEmail('john@example.com');
        $name = 'John Doe';

        $user = User::create($id, $email, $name);

        $this->assertEquals($id, $user->id());
        $this->assertEquals($email, $user->email());
        $this->assertEquals($name, $user->name());

        $events = $user->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserCreatedDomainEvent::class, $events[0]);
    }

    /** @test */
    public function it_should_update_user_name(): void
    {
        $user = User::create(UserId::random(), new UserEmail('john@example.com'), 'John Doe');

        $newName = 'Jane Doe';
        $user->rename($newName);

        $this->assertEquals($newName, $user->name());
    }
}
