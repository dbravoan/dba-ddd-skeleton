<?php

namespace Dba\DddSkeleton\Shared\Infrastructure\Barcodes;

use jucksearm\barcode\lib\QRcodeFactory as BaseQRcodeFactory;

class QRcodeFactory extends BaseQRcodeFactory
{
    public function getPngData()
    {
        return $this->getQRcodePngData();
    }

    public function getSvgData()
    {
        return $this->getQRcodeSvgData();
    }
}
