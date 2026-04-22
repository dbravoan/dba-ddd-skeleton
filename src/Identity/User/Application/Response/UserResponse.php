<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Response;

use Dba\DddSkeleton\Identity\User\Domain\User;
use Dba\DddSkeleton\Shared\Domain\Bus\Query\Response;

final readonly class UserResponse implements Response
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email
    ) {}

    public static function fromAggregate(User $user): self
    {
        return new self(
            $user->id()->value(),
            $user->name(),
            $user->email()->value()
        );
    }

    /** @return array{id: string, name: string, email: string} */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
