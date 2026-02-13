<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain;

interface ImageCreator
{
    public function canvas($width, $height, $color);
    public function make($img);
}
