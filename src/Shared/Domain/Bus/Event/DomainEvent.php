<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Bus\Event;

use DateTimeImmutable;
use Dba\DddSkeleton\Shared\Domain\Utils;
use Dba\DddSkeleton\Shared\Domain\ValueObject\Uuid;

abstract readonly class DomainEvent
{
    private string $aggregateId;
    private string $eventId;
    private string $occurredOn;

    public function __construct(string $aggregateId, ?string $eventId = null, ?string $occurredOn = null)
    {
        $this->aggregateId = $aggregateId;
        $this->eventId     = $eventId ?: Uuid::random()->value();
        $this->occurredOn  = $occurredOn ?: Utils::dateToString(new DateTimeImmutable());
    }

    /** @param array<string, mixed> $body */
    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self;

    abstract public static function eventName(): string;

    /** @return array<string, mixed> */
    abstract public function toPrimitives(): array;

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredOn(): string
    {
        return $this->occurredOn;
    }
}
