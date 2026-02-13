<?php

namespace Dba\DddSkeleton\Shared\Infrastructure\Barcodes;

use jucksearm\barcode\lib\DatamatrixFactory as BaseDatamatrixFactory;

class DatamatrixFactory extends BaseDatamatrixFactory
{
    public function getPngData()
    {
        return $this->getDatamatrixPngData();
    }
}
