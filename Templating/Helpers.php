<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Templating;

use Nette;
use Nette\Utils\Html;
use Schmutzka;
use Schmutzka\Utils\Time;


class Helpers extends Nette\Object
{
	/** @inject @var Nette\Http\IRequest */
	public $httpRequest;


	public function loader($helper)
	{
		if (method_exists($this, $helper)) {
			return callback($this, $helper);
		}
	}


	/**
	 * @param string
	 * @return string
	 */
	public function translate($message)
	{
		return $message;
	}


	/**
	 * @param string
	 * @return string
	 */
	public function phone($number)
	{
		return preg_replace('~.*(\d{3})[^\d]*(\d{3})[^\d]*(\d{3}).*~', '$1 $2 $3', $number);
	}


	/**
	 * @param float
	 * @param int
	 * @return float|int
	 */
	public static function round($n, $precision = 0)
	{
		return round($n, $precision);
	}


	/**
	 * @param string
	 * @param string
	 * @return Html
	 */
	public function email($email, $class = NULL)
	{
		$emailEncoded = NULL;
		for ($x = 0, $_length = strlen($email); $x < $_length; $x++) {
			if (preg_match('!\w!' . 'u', $email[$x])) {
				$emailEncoded .= '%' . bin2hex($email[$x]);

			} else {
				$emailEncoded .= $email[$x];
			}
		}

		$link = Html::el('a')
			->addClass($class)
			->setHref('mailto:' . $emailEncoded)
			->setText($email);

		return $link;
	}


	/**
	 * Joins two datetimes as term (from - to)
	 * @param string/DateTime
	 * @param string/DateTime
	 * @return string
	 */
	public static function term($from, $to)
	{
		$from = new Nette\DateTime($from);
		$to = new Nette\DateTime($to);

		if ($from->format('Y-m-d H:i') == $to->format('Y-m-d H:i')) {
			return $from;
		}

		$dayFrom = $from->format('j. n. Y');
		$dayTo = $to->format('j. n. Y');
		$timeFrom = $from->format('H:i');
		$timeTo = $to->format('H:i');

		if ($from->format('Y') == $to->format('Y')) { // same year

			if ($dayFrom == $dayTo) { // same day
				$term = $dayFrom . ' ' . $timeFrom . ' - ' . $timeTo;

			} else { // different day
				$term = $from->format('j. n.') . '-' . $to->format('j. n. Y') . ' ' . $timeFrom . '-' . $timeTo;
			}

		} else { // different year
			$term = $dayFrom . '  ' . $timeFrom . '-' . $dayTo . ' ' . $timeTo;
		}

		return $term;
	}

}
