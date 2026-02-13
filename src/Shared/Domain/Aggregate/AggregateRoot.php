<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Aggregate;

use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEvent;

abstract class AggregateRoot
{
    private array $domainEvents = [];

    final public function pullDomainEvents(): array
    {
        $domainEvents       = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    final protected function record(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    final protected static function createNullableValueObject(string $className, $value): ?object
    {
        return empty($value) ? null : new $className($value);
    }
}
