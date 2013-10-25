<?php

namespace QrModule\Socials;

use Nette;
use Nette\Utils\Strings;
use QRcode;


class QrGenerator extends Nette\Object
{
	/** @inject @var Schmutzka\ParamService */
	public $paramService;


	/**
	 * Generates QR code image for particular url
	 * @param string $url
	 * @param int $size
	 * @return string $filename
	 */
	public function generateImageForUrl($url, $size = 150)
	{
		$filename = '/images/qr/' . Strings::webalize($url) . '.png';
		$qrcode = new QRcode(utf8_encode($url), 'Q');
		$qrcode->disableBorder();
		$qrcode->displayPNG($size, array(255,255,255), array(0,0,0), $this->paramService->wwwDir . $filename);

		return $filename;
	}

}
