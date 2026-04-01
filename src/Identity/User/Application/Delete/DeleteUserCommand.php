<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Delete;

use Dba\DddSkeleton\Shared\Domain\Bus\Command\Command;

final readonly class DeleteUserCommand implements Command
{
    public function __construct(
        public string $id
    ) {}
}