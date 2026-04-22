<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\Find;

use Dba\DddSkeleton\Shared\Domain\Bus\Query\Query;

final readonly class FindUserQuery implements Query
{
    public function __construct(
        public string $id
    ) {}
}
