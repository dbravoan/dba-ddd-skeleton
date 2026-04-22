<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain;

abstract class NotFoundDomainError extends DomainError
{
    public function errorCode(): string
    {
        return 'not_found';
    }
}
