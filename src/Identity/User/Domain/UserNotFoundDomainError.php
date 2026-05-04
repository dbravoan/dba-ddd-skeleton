<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Domain;

use Dba\DddSkeleton\Shared\Domain\NotFoundDomainError;

final class UserNotFoundDomainError extends NotFoundDomainError
{
    public function __construct(private readonly string $userId)
    {
        parent::__construct();
    }

    protected function errorMessage(): string
    {
        return sprintf('User with id <%s> not found', $this->userId);
    }
}
