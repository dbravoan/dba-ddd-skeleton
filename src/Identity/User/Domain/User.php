<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Domain;

use Dba\DddSkeleton\Shared\Domain\Aggregate\AggregateRoot;

final class User extends AggregateRoot
{
    public function __construct(
        private readonly UserId $id,
        private UserEmail $email,
        private string $name
    ) {}

    public static function create(UserId $id, UserEmail $email, string $name): self
    {
        $user = new self($id, $email, $name);

        $user->record(new UserCreatedDomainEvent($id->value(), $email->value(), $name));

        return $user;
    }

    /** @param array<string, mixed> $primitives */
    public static function fromPrimitives(array $primitives): self
    {
        $id = $primitives['id'] ?? '';
        $email = $primitives['email'] ?? '';
        $name = $primitives['name'] ?? '';

        return new self(
            new UserId(is_string($id) ? $id : ''),
            new UserEmail(is_string($email) ? $email : ''),
            is_string($name) ? $name : ''
        );
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): UserEmail
    {
        return $this->email;
    }

    public function name(): string
    {
        return $this->name;
    }

    /** @return array{id: string, email: string, name: string} */
    public function toPrimitives(): array
    {
        return [
            'id' => $this->id->value(),
            'email' => $this->email->value(),
            'name' => $this->name,
        ];
    }

    public function rename(string $newName): void
    {
        $this->name = $newName;
    }

    public function changeEmail(UserEmail $newEmail): void
    {
        $this->email = $newEmail;
    }
}
