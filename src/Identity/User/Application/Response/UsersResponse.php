<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Response;

use Dba\DddSkeleton\Shared\Domain\Bus\Query\Response;

final readonly class UsersResponse implements Response
{
    /** @var UserResponse[] */
    private array $users;

    public function __construct(UserResponse ...$users)
    {
        $this->users = $users;
    }

    /**
     * @return UserResponse[]
     */
    public function users(): array
    {
        return $this->users;
    }

    /**
     * Transforms the collection into a plain array for 
     * infrastructure delivery (Inertia/API).
     */
    public function toArray(): array
    {
        return array_map(
            fn (UserResponse $response) => $response->toArray(), 
            $this->users
        );
    }
}