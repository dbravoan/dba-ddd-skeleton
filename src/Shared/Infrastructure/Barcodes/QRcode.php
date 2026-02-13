<?php

namespace Dba\DddSkeleton\Shared\Infrastructure\Barcodes;

use jucksearm\barcode\QRcode as BaseQRcode;

class QRcode extends BaseQRcode
{
	public static function factory()
	{
		return new QRcodeFactory();
	}

	public static function getPng(
		$code,
		$emblem = null,
		$file = null,
		$level = null,
		$size = null,
		$margin = null,
		$color = null
	) {
		$qrcodeFactory = self::factory()
			->setCode($code)
			->setEmblem($emblem)
			->setFile($file)
			->setLevel($level)
			->setSize($size)
			->setMargin($margin)
			->setColor($color);

		return $qrcodeFactory->getPngData();
	}

	public static function getSvg(
		$code,
		$emblem = null,
		$file = null,
		$level = null,
		$size = null,
		$margin = null,
		$color = null
	) {
		$qrcodeFactory = self::factory()
			->setCode($code)
			->setEmblem($emblem)
			->setFile($file)
			->setLevel($level)
			->setSize($size)
			->setMargin($margin)
			->setColor($color);

		return $qrcodeFactory->getSvgData();
	}
}
