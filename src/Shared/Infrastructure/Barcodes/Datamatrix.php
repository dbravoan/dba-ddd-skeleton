<?php

namespace Dba\DddSkeleton\Shared\Infrastructure\Barcodes;

use jucksearm\barcode\Datamatrix as BaseDatamatrix;

class Datamatrix extends BaseDatamatrix
{
    public static function factory()
    {
        return new DatamatrixFactory();
    }

    public static function getPng(
        $code,
        $file = null,
        $size = null,
        $margin = null,
        $color = null
    ) {
        $datamatrixFactory = self::factory()
            ->setCode($code)
            ->setFile($file)
            ->setSize($size)
            ->setMargin($margin)
            ->setColor($color);

        return $datamatrixFactory->getPngData();
    }
}
