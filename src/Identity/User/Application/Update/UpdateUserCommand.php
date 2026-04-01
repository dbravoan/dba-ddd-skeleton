<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Update;

use Dba\DddSkeleton\Shared\Domain\Bus\Command\Command;

final readonly class UpdateUserCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email
    ) {}
}