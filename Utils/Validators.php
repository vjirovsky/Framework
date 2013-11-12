<?php

namespace Schmutzka\Utils;

use Nette;
use DateTime;


class Validators extends Nette\Utils\Validators
{

	/**
	 * @param string
	 * @return bool
	 */
	public static function isDateTime($value)
	{
		return preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $value);
	}


	/**
	 * @return bool
	 */
	public static function isTime($time)
	{
		return (preg_match('/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/', $time) || preg_match('/^([0-9]):([0-5][0-9])$/', $time));
	}


	/**
	 * @param string
	 * @return FALSE|Nette\DateTime
	 */
	public static function isDate($date)
	{
		if (strpos($date, '-')) { // A. world format
			$dateArray = explode('-', $date, 3);
			list($y, $m, $d) = $dateArray;

		} elseif (strpos($date, '.')) { // B. czech format

			$dateArray = explode('.', $date, 3);
			list($d, $m, $y) = $dateArray;

			$d = trim($d);
			$m = trim($m);
			$y = trim($y);

		} else {
			return FALSE;
		}

		$dateForm = $y . '-' . $m . '-' . $d;

		if (checkdate((int) $m,(int) $d,(int) $y)) {
			return new Nette\DateTime($dateForm);

		} else {
			return FALSE;
		}
	}

}
