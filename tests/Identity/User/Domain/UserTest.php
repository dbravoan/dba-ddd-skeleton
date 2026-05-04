<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Domain;

use Dba\DddSkeleton\Identity\User\Domain\User;
use Dba\DddSkeleton\Identity\User\Domain\UserCreatedDomainEvent;
use Dba\DddSkeleton\Identity\User\Domain\UserEmail;
use Dba\DddSkeleton\Identity\User\Domain\UserId;
use Dba\DddSkeleton\Identity\User\Domain\UserName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    #[Test]
    public function it_should_create_a_user(): void
    {
        $id = UserId::random();
        $email = new UserEmail('john@example.com');
        $name = new UserName('John Doe');

        $user = User::create($id, $email, $name);

        $this->assertEquals($id, $user->id());
        $this->assertEquals($email, $user->email());
        $this->assertEquals($name->value(), $user->name()->value());

        $events = $user->pullDomainEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(UserCreatedDomainEvent::class, $events[0]);
    }

    #[Test]
    public function it_should_update_user_name(): void
    {
        $user = User::create(UserId::random(), new UserEmail('john@example.com'), new UserName('John Doe'));

        $newName = new UserName('Jane Doe');
        $user->rename($newName);

        $this->assertEquals($newName->value(), $user->name()->value());
    }

    #[Test]
    public function it_should_pull_domain_events_only_once(): void
    {
        $user = User::create(UserId::random(), new UserEmail('a@b.com'), new UserName('A'));

        $this->assertCount(1, $user->pullDomainEvents());
        $this->assertCount(0, $user->pullDomainEvents());
    }

    #[Test]
    public function it_should_reconstruct_from_primitives(): void
    {
        $id = UserId::random();
        $email = new UserEmail('john@example.com');
        $name = new UserName('John Doe');

        $user = User::create($id, $email, $name);
        $user->pullDomainEvents();

        $primitives = $user->toPrimitives();

        $this->assertSame($id->value(), $primitives['id']);
        $this->assertSame($email->value(), $primitives['email']);
        $this->assertSame($name->value(), $primitives['name']);
    }
}
