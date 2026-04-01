<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Event\Laravel;

use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEvent;
use Dba\DddSkeleton\Shared\Domain\Bus\Event\EventBus;

final class LaravelQueueEventBus implements EventBus
{
    public function __construct(
        private readonly string $queue = 'domain_events',
        private readonly ?string $connection = null,
    ) {}

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $job = ProcessDomainEventJob::fromDomainEvent($event)
                ->onQueue($this->queue);

            if ($this->connection !== null) {
                $job->onConnection($this->connection);
            }

            dispatch($job);
        }
    }
}
