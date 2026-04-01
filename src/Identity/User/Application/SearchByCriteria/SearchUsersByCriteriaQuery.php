<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Identity\User\Application\SearchByCriteria;

use Dba\DddSkeleton\Shared\Domain\Bus\Query\Query;

final readonly class SearchUsersByCriteriaQuery implements Query
{
    public function __construct(
        public array $filters,
        public ?string $orderBy = null,
        public ?string $orderType = null,
        public ?int $limit = null,
        public ?int $offset = null
    ) {}
}