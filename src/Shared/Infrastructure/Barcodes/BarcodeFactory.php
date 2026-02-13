<?php

namespace Dba\DddSkeleton\Shared\Infrastructure\Barcodes;

use jucksearm\barcode\lib\BarcodeFactory as BaseBarcodeFactory;

class BarcodeFactory extends BaseBarcodeFactory
{
    public function getPngData()
    {
        return $this->getBarcodePngData();
    }

    public function getSvgData()
    {
        return $this->getBarcodeSvgData();
    }
}
