<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain;

abstract class BadRequestDomainError extends DomainError
{
    public function errorCode(): string
    {
        return 'bad_request';
    }
}
