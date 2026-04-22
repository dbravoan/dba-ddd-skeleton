<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Domain;

use Dba\DddSkeleton\Shared\Domain\Bus\Event\DomainEvent;

final readonly class UserCreatedDomainEvent extends DomainEvent
{
    public function __construct(
        string $id,
        private string $email,
        private string $name,
        ?string $eventId = null,
        ?string $occurredOn = null
    ) {
        parent::__construct($id, $eventId, $occurredOn);
    }

    public static function eventName(): string
    {
        return 'user.created';
    }

    /** @param array<string, mixed> $body */
    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self {
        $email = $body['email'] ?? '';
        $name = $body['name'] ?? '';

        return new self(
            $aggregateId,
            is_string($email) ? $email : '',
            is_string($name) ? $name : '',
            $eventId,
            $occurredOn
        );
    }

    /** @return array{email: string, name: string} */
    public function toPrimitives(): array
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
        ];
    }

    public function email(): string
    {
        return $this->email;
    }

    public function name(): string
    {
        return $this->name;
    }
}
