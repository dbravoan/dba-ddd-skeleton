<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Tests\Shared\Infrastructure\Bus\Event;

use Dba\DddSkeleton\Identity\User\Domain\UserCreatedDomainEvent;
use Dba\DddSkeleton\Shared\Infrastructure\Bus\Event\DomainEventJsonSerializer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DomainEventJsonSerializerTest extends TestCase
{
    #[Test]
    public function it_should_serialize_a_domain_event_to_json(): void
    {
        $event = new UserCreatedDomainEvent('user-id-1', 'john@example.com', 'John Doe');
        $serializer = new DomainEventJsonSerializer;

        $json = $serializer->serialize($event);

        $this->assertJson($json);

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json, true);

        $this->assertArrayHasKey('data', $decoded);
        $this->assertSame(UserCreatedDomainEvent::eventName(), $decoded['data']['type']);
        $this->assertSame('user-id-1', $decoded['data']['attributes']['id']);
    }

    #[Test]
    public function it_should_include_event_id_and_occurred_on(): void
    {
        $event = new UserCreatedDomainEvent('user-id-2', 'jane@example.com', 'Jane Doe');
        $serializer = new DomainEventJsonSerializer;

        $json = $serializer->serialize($event);

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($json, true);

        $this->assertArrayHasKey('id', $decoded['data']);
        $this->assertArrayHasKey('occurred_on', $decoded['data']);
        $this->assertNotEmpty($decoded['data']['id']);
        $this->assertNotEmpty($decoded['data']['occurred_on']);
    }
}
