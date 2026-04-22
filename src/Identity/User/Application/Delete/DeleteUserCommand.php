<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Delete;

use Dba\DddSkeleton\Shared\Domain\Bus\Command\Command;

final readonly class DeleteUserCommand implements Command
{
    public function __construct(private string $id) {}

    public function id(): string
    {
        return $this->id;
    }
}
