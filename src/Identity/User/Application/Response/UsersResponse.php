<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Response;

use Dba\DddSkeleton\Shared\Domain\Bus\Query\Response;

final readonly class UsersResponse implements Response
{
    /**
     * @param  array<int, UserResponse>  $users
     */
    public function __construct(private array $users) {}

    /**
     * @return array<int, UserResponse>
     */
    public function users(): array
    {
        return $this->users;
    }

    /**
     * @return array<int, array{id: string, name: string, email: string}>
     */
    public function toArray(): array
    {
        return array_map(
            fn (UserResponse $response) => $response->toArray(),
            $this->users
        );
    }
}
