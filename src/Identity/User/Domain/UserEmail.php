<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Domain;

use Dba\DddSkeleton\Shared\Domain\ValueObject\StringValueObject;
use InvalidArgumentException;

final class UserEmail extends StringValueObject
{
    public function __construct(protected string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('<%s> is not a valid email.', $value));
        }
        parent::__construct($value);
    }
}