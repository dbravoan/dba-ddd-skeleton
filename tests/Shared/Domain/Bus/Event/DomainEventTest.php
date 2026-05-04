<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\Bus\Event;

use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final readonly class StubDomainEvent extends DomainEvent
{
    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredOn): static
    {
        return new self($aggregateId, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'test.event';
    }

    public function toPrimitives(): array
    {
        return [];
    }
}

final class DomainEventTest extends TestCase
{
    #[Test]
    public function it_should_auto_generate_event_id_and_occurred_on(): void
    {
        $event = new StubDomainEvent('some-aggregate-id');

        $this->assertSame('some-aggregate-id', $event->aggregateId());
        $this->assertTrue(Uuid::isValid($event->eventId()));
        $this->assertNotEmpty($event->occurredOn());
    }

    #[Test]
    public function it_should_use_provided_event_id_and_occurred_on(): void
    {
        $eventId = Uuid::uuid4()->toString();
        $occurredOn = '2024-01-01 00:00:00';

        $event = new StubDomainEvent('agg-id', $eventId, $occurredOn);

        $this->assertSame($eventId, $event->eventId());
        $this->assertSame($occurredOn, $event->occurredOn());
    }

    #[Test]
    public function it_should_reconstruct_from_primitives(): void
    {
        $eventId = Uuid::uuid4()->toString();
        $occurredOn = '2024-06-01 12:00:00';

        $event = new StubDomainEvent('agg-id-reconstruct', $eventId, $occurredOn);
        $reconstructed = $event::fromPrimitives(
            $event->aggregateId(),
            $event->toPrimitives(),
            $event->eventId(),
            $event->occurredOn()
        );

        $this->assertSame($event->aggregateId(), $reconstructed->aggregateId());
        $this->assertSame($event->eventId(), $reconstructed->eventId());
        $this->assertSame($event->occurredOn(), $reconstructed->occurredOn());
    }
}
