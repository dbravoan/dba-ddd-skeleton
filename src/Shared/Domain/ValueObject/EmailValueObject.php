<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain\ValueObject;

use InvalidArgumentException;

abstract readonly class EmailValueObject extends StringValueObject
{
    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->ensureIsValidEmail($value);
    }

    private function ensureIsValidEmail(string $email): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $email));
        }
    }
}
