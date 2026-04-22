<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Event\Laravel;

use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEvent;
use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEventSubscriber;
use Dba\DddSkeleton\Shared\Domain\Bus\Event\EventBus;

final class LaravelEventBus implements EventBus
{
    /** @var array<string, array<int, DomainEventSubscriber>> */
    private array $subscribersByEvent = [];

    /**
     * @param iterable<DomainEventSubscriber> $subscribers
     */
    public function __construct(iterable $subscribers)
    {
        $this->mapSubscribers($subscribers);
    }

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $subscribers = $this->subscribersByEvent[$event::class] ?? [];

            foreach ($subscribers as $subscriber) {
                /** @var callable $subscriber */
                $subscriber($event);
            }
        }
    }

    /**
     * @param iterable<DomainEventSubscriber> $subscribers
     */
    private function mapSubscribers(iterable $subscribers): void
    {
        foreach ($subscribers as $subscriber) {
            foreach ($subscriber::subscribedTo() as $eventClass) {
                $this->subscribersByEvent[$eventClass][] = $subscriber;
            }
        }
    }
}
