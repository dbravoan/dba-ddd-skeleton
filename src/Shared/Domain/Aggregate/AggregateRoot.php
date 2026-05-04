<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Aggregate;

use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEvent;

abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    /** @return DomainEvent[] */
    final public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    final protected function record(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    protected function createNullableValueObject(string $class, mixed $value): ?object
    {
        return ($value === null || $value === '') ? null : new $class($value);
    }
}
