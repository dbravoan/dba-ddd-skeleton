<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure;

use Intervention\Image\Facades\Image;
use Dba\DddSkeleton\Shared\Domain\ImageCreator;

class InterventionImage implements ImageCreator
{
    public function canvas($width, $height, $color)
    {
        return Image::canvas($width, $height, $color);
    }
    public function make($image)
    {
        return Image::make($image);
    }
}
