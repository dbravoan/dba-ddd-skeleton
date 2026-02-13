<?php

declare(strict_types=1);

namespace Dba\DddSkeleton\Shared\Infrastructure;

use Dba\DddSkeleton\Shared\Domain\BarcodeGenerator;
use Dba\DddSkeleton\Shared\Infrastructure\Barcodes\Barcode;
use Dba\DddSkeleton\Shared\Infrastructure\Barcodes\Datamatrix;
use Dba\DddSkeleton\Shared\Infrastructure\Barcodes\QRcode;

class JucksearmBarcode implements BarcodeGenerator
{
    public function barcodePng($code, $type = null, $file = null, $scale = null, $height = null, $rotate = null, $color = null)
    {
        return Barcode::getPng($code, $type, $file, $scale, $height, $rotate, $color);
    }
    public function datamatrixPng($code, $file = null, $size = null, $margin = null, $color = null)
    {
        return Datamatrix::getPng($code, $file, $size, $margin, $color);
    }
    public function qrcodePng($code, $emblem, $file, $level, $size, $margin, $color)
    {
        return QRcode::getPng($code, $emblem, $file, $level, $size, $margin, $color);
    }
}
