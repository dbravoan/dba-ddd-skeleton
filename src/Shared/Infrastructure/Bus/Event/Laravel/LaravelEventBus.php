<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Event\Laravel;

use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEvent;
use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEventSubscriber;
use Dba\DddSkeleton\Shared\Domain\Bus\Event\EventBus;

final class LaravelEventBus implements EventBus
{
    /** @var array<string, DomainEventSubscriber[]> */
    private array $subscribersByEvent = [];

    public function __construct(iterable $subscribers)
    {
        $this->mapSubscribers($subscribers);
    }

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $subscribers = $this->subscribersByEvent[$event::class] ?? [];

            foreach ($subscribers as $subscriber) {
                $subscriber($event);
            }
        }
    }

    private function mapSubscribers(iterable $subscribers): void
    {
        foreach ($subscribers as $subscriber) {
            if (!$subscriber instanceof DomainEventSubscriber) {
                continue;
            }

            foreach ($subscriber::subscribedTo() as $eventClass) {
                $this->subscribersByEvent[$eventClass][] = $subscriber;
            }
        }
    }
}
