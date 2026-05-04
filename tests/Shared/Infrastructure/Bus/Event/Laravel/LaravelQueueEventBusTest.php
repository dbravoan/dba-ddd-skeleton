<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Infrastructure\Bus\Event\Laravel;

use Dba\DddSkeleton\Identity\User\Domain\UserCreatedDomainEvent;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\Event\Laravel\LaravelQueueEventBus;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\Event\Laravel\ProcessDomainEventJob;
use Dba\DddSkeleton\Tests\DbaTestCase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;

final class LaravelQueueEventBusTest extends DbaTestCase
{
    #[Test]
    public function it_should_dispatch_a_job_for_each_event(): void
    {
        Queue::fake();

        $bus = new LaravelQueueEventBus(queue: 'domain_events');

        $event = new UserCreatedDomainEvent(
            id: '550e8400-e29b-41d4-a716-446655440000',
            email: 'john@example.com',
            name: 'John Doe'
        );

        $bus->publish($event);

        Queue::assertPushed(ProcessDomainEventJob::class, 1);
    }

    #[Test]
    public function it_should_dispatch_one_job_per_event(): void
    {
        Queue::fake();

        $bus = new LaravelQueueEventBus(queue: 'domain_events');

        $eventA = new UserCreatedDomainEvent('id-a', 'a@example.com', 'Alice');
        $eventB = new UserCreatedDomainEvent('id-b', 'b@example.com', 'Bob');

        $bus->publish($eventA, $eventB);

        Queue::assertPushed(ProcessDomainEventJob::class, 2);
    }
}
