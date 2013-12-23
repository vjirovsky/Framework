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


class Finder extends Nette\Utils\Finder
{

	/**
	 * Convert found files to array list
	 * @return array
	 */
	public function toArray()
	{
		$array = array();
		foreach ($this as $name => $info) {
			$uid = pathinfo($info->getFilename(), PATHINFO_FILENAME);
			$array[$uid] = $name;
		}

		return $array;
	}

}
