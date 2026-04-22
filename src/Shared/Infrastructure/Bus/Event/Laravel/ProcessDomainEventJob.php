<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Event\Laravel;

use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

final class ProcessDomainEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @param class-string<DomainEvent> $eventClass
     * @param array<string, mixed> $body
     */
    public function __construct(
        private readonly string $eventClass,
        private readonly string $aggregateId,
        private readonly array $body,
        private readonly string $eventId,
        private readonly string $occurredOn,
    ) {}

    public static function fromDomainEvent(DomainEvent $event): self
    {
        return new self(
            eventClass: $event::class,
            aggregateId: $event->aggregateId(),
            body: $event->toPrimitives(),
            eventId: $event->eventId(),
            occurredOn: $event->occurredOn(),
        );
    }

    /**
     * Type-hints LaravelEventBus (not EventBus) to avoid infinite recursion
     * when EventBus is bound to LaravelQueueEventBus.
     */
    public function handle(LaravelEventBus $syncBus): void
    {
        /** @var DomainEvent $event */
        $event = ($this->eventClass)::fromPrimitives(
            $this->aggregateId,
            $this->body,
            $this->eventId,
            $this->occurredOn,
        );

        $syncBus->publish($event);
    }
}
