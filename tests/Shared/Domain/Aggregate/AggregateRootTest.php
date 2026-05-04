<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Domain\Aggregate;

use Dba\DddSkeleton\Shared\Domain\Aggregate\AggregateRoot;
use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final readonly class StubAggregateEvent extends DomainEvent
{
    public static function fromPrimitives(string $aggregateId, array $body, string $eventId, string $occurredOn): static
    {
        return new self($aggregateId, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'test.stub';
    }

    public function toPrimitives(): array
    {
        return [];
    }
}

final class AggregateRootTest extends TestCase
{
    #[Test]
    public function it_should_record_domain_events(): void
    {
        $aggregate = new class extends AggregateRoot
        {
            public function doSomething(): void
            {
                $this->record(new StubAggregateEvent('agg-id-1'));
            }
        };

        $aggregate->doSomething();

        $this->assertCount(1, $aggregate->pullDomainEvents());
    }

    #[Test]
    public function it_should_clear_events_after_pulling(): void
    {
        $aggregate = new class extends AggregateRoot
        {
            public function doSomething(): void
            {
                $this->record(new StubAggregateEvent('agg-id-2'));
            }
        };

        $aggregate->doSomething();
        $aggregate->pullDomainEvents();

        $this->assertCount(0, $aggregate->pullDomainEvents());
    }

    #[Test]
    public function it_should_record_multiple_events(): void
    {
        $aggregate = new class extends AggregateRoot
        {
            public function doMultiple(): void
            {
                for ($i = 0; $i < 3; $i++) {
                    $this->record(new StubAggregateEvent('agg-id-3'));
                }
            }
        };

        $aggregate->doMultiple();

        $this->assertCount(3, $aggregate->pullDomainEvents());
    }
}
