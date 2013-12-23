<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Utils;

use Nette;


class Pregi extends Nette\Object
{

	/**
	 * Remove links from string
	 * @param string
	 */
	public static function removeLinks($string)
	{
		$pattern = "~(<a href='[^']'>)([^<]*)(</a>)~";
		$string = preg_replace($pattern, '$2', $string);

		return $string;
	}


	/**
	 * Get number from string
	 * @param string
	 * @return int
	 */
	public function number($string)
	{
		preg_match('/(\d+)/', $string, $matches);
		if ($matches) {
			return $matches[0];
		}

		return NULL;
	}

}
