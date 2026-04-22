<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain;

interface ImageCreator
{
    public function canvas(int $width, int $height, string $color): void;

    public function make(mixed $img): void;
}
