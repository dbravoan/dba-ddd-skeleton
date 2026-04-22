<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\ValueObject;

abstract readonly class PasswordValueObject extends StringValueObject
{
    // Hashing logic usually belongs in Application service, but we can have
    // a base class for type safety.
}
