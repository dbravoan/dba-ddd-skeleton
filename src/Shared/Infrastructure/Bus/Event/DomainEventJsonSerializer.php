<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure\Bus\Event;

use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEvent;

final class DomainEventJsonSerializer
{
    public function serialize(DomainEvent $domainEvent): string
    {
        $data = json_encode([
            'data' => [
                'id' => $domainEvent->eventId(),
                'type' => $domainEvent::eventName(),
                'occurred_on' => $domainEvent->occurredOn(),
                'attributes' => array_merge($domainEvent->toPrimitives(), ['id' => $domainEvent->aggregateId()]),
            ],
            'meta' => [],
        ]);

        if ($data === false) {
            throw new \RuntimeException('Unable to serialize domain event');
        }

        return $data;
    }
}
