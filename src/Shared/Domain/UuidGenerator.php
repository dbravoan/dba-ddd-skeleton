<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain;

interface UuidGenerator
{
    public function generate(): string;
}
