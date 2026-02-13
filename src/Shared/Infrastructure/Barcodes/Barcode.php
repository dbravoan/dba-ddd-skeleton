<?php

namespace Dba\DddSkeleton\Shared\Infrastructure\Barcodes;

use jucksearm\barcode\Barcode as BaseBarcode;

class Barcode extends BaseBarcode
{
    public static function factory()
    {
        return new BarcodeFactory();
    }

    public static function getPng(
        $code,
        $type = null,
        $file = null,
        $scale = null,
        $height = null,
        $rotate = null,
        $color = null
    ) {
        $barcodeFactory = self::factory()
            ->setCode($code)
            ->setType($type)
            ->setFile($file)
            ->setScale($scale)
            ->setHeight($height)
            ->setRotate($rotate)
            ->setColor($color);

        return $barcodeFactory->getPngData();
    }

    public static function getSvg(
        $code,
        $type = null,
        $file = null,
        $scale = null,
        $height = null,
        $rotate = null,
        $color = null
    ) {
        $barcodeFactory = self::factory()
            ->setCode($code)
            ->setType($type)
            ->setFile($file)
            ->setScale($scale)
            ->setHeight($height)
            ->setRotate($rotate)
            ->setColor($color);

        return $barcodeFactory->getSvgData();
    }
}
