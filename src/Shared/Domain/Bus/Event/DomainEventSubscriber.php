<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\Bus\Event;

interface DomainEventSubscriber
{
    /**
     * @return array<int, string>
     */
    public static function subscribedTo(): array;
}
