<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Create;

use Dba\DddSkeleton\Shared\Domain\Bus\Command\Command;

final readonly class CreateUserCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email
    ) {}
}
