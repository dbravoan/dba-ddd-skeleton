<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Infrastructure\Bus\Event;

use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEvent;
use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEventSubscriber;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\Event\Laravel\LaravelEventBus;
use PHPUnit\Framework\TestCase;

final class LaravelEventBusTest extends TestCase
{
    /** @test */
    public function it_should_publish_events_to_subscribers(): void
    {
        $event = new StubDomainEvent('aggregate-id');
        $subscriber = new StubDomainEventSubscriber;
        $bus = new LaravelEventBus([$subscriber]);

        $bus->publish($event);

        $this->assertTrue($subscriber->hasBeenCalled());
    }
}

final class StubDomainEvent extends DomainEvent
{
    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredOn): DomainEvent
    {
        return new self($aggregateId, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'stub.event';
    }

    public function toPrimitives(): array
    {
        return [];
    }
}

final class StubDomainEventSubscriber implements DomainEventSubscriber
{
    private bool $called = false;

    public static function subscribedTo(): array
    {
        return [StubDomainEvent::class];
    }

    public function __invoke(DomainEvent $event): void
    {
        $this->called = true;
    }

    public function hasBeenCalled(): bool
    {
        return $this->called;
    }
}
