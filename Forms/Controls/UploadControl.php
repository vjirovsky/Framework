<?php

namespace Schmutzka\Forms\Controls;

use Nette;


/**
 * @method getResize()
 */
class UploadControl extends Nette\Forms\Controls\UploadControl
{
	/** @var array */
	private $resize;


	/**
	 * @param int
	 * @param int
	 */
	public function addResize($width = NULL, $height = NULL)
	{
		$this->resize[] = [
			'width' => $width,
			'height' => $height
		];

		return $this;
	}

}
