<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain;

interface BarcodeGenerator
{
    public function barcodePng($code, $type, $file, $scale, $height, $rotate, $color);
    public function datamatrixPng($code, $file, $size, $margin, $color);
    public function qrcodePng($code, $emblem, $file, $level, $size, $margin, $color);
}
