<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Utils;

use Nette;
use Nette\Utils\Strings;


class Time extends Nette\Object
{

	/**
	 * Conver one number to another
	 * @param int
	 * @param string { [ d, h, m, s ] }
	 * @param string { [ d, h, m, s ] }
	 * @return  int|string
	 */
	public static function convert($time, $from, $to)
	{
		$result = NULL;
		if ($from == 's') {
			switch ($to) {
				case 'm' :
					$time /= 30;
				case 'd' :
					$time /= 24;
				case 'h' :
					$time /= 60;
				case 'm' :
					$time /= 60;
					break;
			}
			return $time;

		} elseif ($from == 'h:m') {
			list ($h, $m) = explode(':', $time);
			switch ($to) {
				case 's' :
					return $h * 60 * 60 + $m * 60;

				case 'h' :
					return $h + $m/60;

				case 'm' :
					return 60 * $h + $m;

				case 'h:0':
					return $h . ':00';
			}

		} elseif ($from == 'm:s') {
			list ($m, $s) = explode(':', $time);
			switch ($to) {
				case 's' :
					return $m * 60 + $s;
			}

		} elseif ($from == 'm') {
			$h = floor(($time)/60);
			$m = $time - ($h * 60);
			switch ($to) {
				case 'h:m' :
					return $h . ':' . $m;

				case 'h:mm' :
					return $h . ':' . Strings::padLeft($m, 2, 0);


				case 'h:m hod/min' :
					if ($h) {
						return $h . ':' . Strings::padLeft($m, 2, 0) . ' hod.';

					} else {
						return $m . ' min.';
					}
			}

		} elseif ($from == 'h:m:s') {
			$temp = explode(':', $time);
			if (count($temp) < 3) {
				return $time;
			}

			list ($h, $m, $s) = $temp;
			switch ($to) {
				case 's' :
					return $h * 60 * 60 + $m * 60 + $s;
					break;
			}

		}

		throw new \Exception('Not defined yet');
	}

}
