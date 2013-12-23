<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

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
