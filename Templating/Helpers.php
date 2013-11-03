<?php

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
	 * @param array
	 * @param int
	 * @param string
	 * @param string
	 * @return string
	 */
	public static function number($number, $decimals = 0, $dec_point = ',', $thousand_sep = ' ')
	{
		return number_format($number, $decimals, $dec_point, $thousand_sep);
	}


	/**
	 * @param mixed
	 * @param string
	 * @param string
	 * @return string
	 */
	public function isEmpty($value, $emptyReturn = '-', $notEmptyReturn = NULL)
	{
		if ((!isset($value)) || (!$value && !is_numeric($value)) || is_null($value)) {
			return $emptyReturn;

		} else {
			return trim($value . ' ' . $notEmptyReturn);
		}
	}


	/**
	  * @param string|int
	  * @param string
	  */
	public function inArray($value, $array, $return = '-')
	{
		if (isset($array[$value])) {
			return $array[$value];

		} else {
			return $return;
		}
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
	 * Converts date to words in Czech
	 * @param mixed
	 * @return string
	 */
	public static function dateAgoInWords($date)
	{
		if (!$date) {
			return FALSE;

		} elseif (is_numeric($date)) {
			$date = (int) $date;

		} elseif ($date instanceof DateTime) {
			$date = $date->format('U');

		} else {
			$date = strtotime($date);
		}

		$now = time();
		// @todo fix
		$delta = self::diffInDays($date, $now);

		if ($delta < 0) {
			$delta = abs($delta);
			if ($delta == 0) return 'ještě dnes';
			if ($delta == 1) return 'zítra';
			if ($delta < 30) return 'za ' . $delta . ' ' . self::plural($delta, 'den', 'dny', 'dní');
			if ($delta < 60) return 'za měsíc';
			if ($delta < 365) return 'za ' . round($delta / 30) . ' ' . self::plural(round($delta / 30), 'měsíc', 'měsíce', 'měsícù');
			if ($delta < 730) return 'za rok';
			return 'za ' . round($delta / 365) . ' ' . self::plural(round($delta / 365), 'rok', 'roky', 'let');
		}

		if ($delta == 0) return 'dnes';
		if ($delta == 1) return 'včera';
		if ($delta < 30) return 'před ' . $delta . ' dny';
		if ($delta < 60) return 'před měsícem';
		if ($delta < 365) return 'před ' . round($delta / 30) . ' měsíci';
		if ($delta < 730) return 'před rokem';
		return 'před ' . round($delta / 365) . ' lety';
	}


	/**
	 * Converts time to words in Czech
	 * @see http://addons.nette.org/cs/helper-time-ago-in-words.
	 * @param mixed $time
	 * @return string
	 */
	public static function timeAgoInWords($time)
	{
		if (!$time) {
			return FALSE;

		} elseif (is_numeric($time)) {
			$time = (int) $time;

		} elseif ($time instanceof DateTime) {
			$time = $time->format('U');

		} else {
			$time = strtotime($time);
		}

		$delta = time() - $time;

		if ($delta < 0) {
			$delta = round(abs($delta) / 60);
			if ($delta == 0) return 'za okamžik';
			if ($delta == 1) return 'za minutu';
			if ($delta < 45) return 'za ' . $delta . ' ' . self::plural($delta, 'minuta', 'minuty', 'minut');
			if ($delta < 90) return 'za hodinu';
			if ($delta < 1440) return 'za ' . round($delta / 60) . ' ' . self::plural(round($delta / 60), 'hodina', 'hodiny', 'hodin');
			if ($delta < 2880) return 'zítra';
			if ($delta < 43200) return 'za ' . round($delta / 1440) . ' ' . self::plural(round($delta / 1440), 'den', 'dny', 'dní');
			if ($delta < 86400) return 'za měsíc';
			if ($delta < 525960) return 'za ' . round($delta / 43200) . ' ' . self::plural(round($delta / 43200), 'měsíc', 'měsíce', 'měsícù');
			if ($delta < 1051920) return 'za rok';
			return 'za ' . round($delta / 525960) . ' ' . self::plural(round($delta / 525960), 'rok', 'roky', 'let');
		}

		$delta = round($delta / 60);
		if ($delta == 0) return 'před okamžikem';
		if ($delta == 1) return 'před minutou';
		if ($delta < 45) return 'před $delta minutami';
		if ($delta < 90) return 'před hodinou';
		if ($delta < 1440) return 'před ' . round($delta / 60) . ' hodinami';
		if ($delta < 2880) return 'včera';
		if ($delta < 43200) return 'před ' . round($delta / 1440) . ' dny';
		if ($delta < 86400) return 'před měsícem';
		if ($delta < 525960) return 'pečd ' . round($delta / 43200) . ' měsíci';
		if ($delta < 1051920) return 'před rokem';

		return 'před ' . round($delta / 525960) . ' lety';
	}


	/**
	 * @param  int
	 * @return mixed
	 */
	public static function plural($n)
	{
		$args = func_get_args();
		return $args[($n == 1) ? 1 : (($n >= 2 && $n <= 4) ? 2 : 3)];
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
