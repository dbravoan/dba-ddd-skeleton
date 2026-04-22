<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Domain;

interface BarcodeGenerator
{
    public function barcodePng(string $code, string $type, string $file, int|float $scale, int|float $height, int|float $rotate, string $color): void;

    public function datamatrixPng(string $code, string $file, int|float $size, int|float $margin, string $color): void;

    public function qrcodePng(string $code, ?string $emblem, string $file, string $level, int|float $size, int|float $margin, string $color): void;
}
