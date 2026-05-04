<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Identity\User\Domain;

use Dba\DddSkeleton\Identity\User\Domain\UserCreatedDomainEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserCreatedDomainEventTest extends TestCase
{
    #[Test]
    public function it_should_have_the_correct_event_name(): void
    {
        $this->assertSame('user.created', UserCreatedDomainEvent::eventName());
    }

    #[Test]
    public function it_should_expose_email_and_name(): void
    {
        $event = new UserCreatedDomainEvent(
            id: '550e8400-e29b-41d4-a716-446655440000',
            email: 'john@example.com',
            name: 'John Doe'
        );

        $this->assertSame('john@example.com', $event->email());
        $this->assertSame('John Doe', $event->name());
    }

    #[Test]
    public function it_should_convert_to_primitives(): void
    {
        $event = new UserCreatedDomainEvent(
            id: '550e8400-e29b-41d4-a716-446655440000',
            email: 'john@example.com',
            name: 'John Doe'
        );

        $primitives = $event->toPrimitives();

        $this->assertSame('john@example.com', $primitives['email']);
        $this->assertSame('John Doe', $primitives['name']);
    }

    #[Test]
    public function it_should_build_from_primitives(): void
    {
        $event = UserCreatedDomainEvent::fromPrimitives(
            aggregateId: '550e8400-e29b-41d4-a716-446655440000',
            body: ['email' => 'john@example.com', 'name' => 'John Doe'],
            eventId: 'event-uuid',
            occurredOn: '2024-01-01T00:00:00+00:00'
        );

        $this->assertSame('john@example.com', $event->email());
        $this->assertSame('John Doe', $event->name());
        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $event->aggregateId());
    }
}
