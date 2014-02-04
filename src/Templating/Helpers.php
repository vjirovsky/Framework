<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Templating;

use DateTime;
use Nette;
use Nette\Utils\Html;
use Zenify;
use Zenify\Utils\Time;


class Helpers extends Nette\Object
{
	/** @inject @var Nette\Http\IRequest */
	public $httpRequest;

	/** @var string[] */
	private $weekDayLocalized = [
		'cs' => [
			'short' => [1 => 'Po', 'Út', 'St', 'Čt', 'Pá', 'So', 'Ne'],
			'long' => [1 => 'pondělí', 'úterý', 'středa', 'čtvtek', 'pátek', 'sobota', 'neděle']
		]
	];

	/** @var string[] */
	private $monthLocalized = [
		'cs' => [1 => 'leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec']
	];


	/** @var Nette\Localization\ITranslator */
	private $translator;


	public function __construct(Nette\Localization\ITranslator $translator = NULL)
	{
		$this->translator = $translator;
	}



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
	 * @param DateTime
	 * @param bool
	 * @param string
	 * @return string
	 */
	public function weekday(DateTime $date, $ucfirst = TRUE, $type = 'long')
	{
		if ($this->translator) {
			$lang = $this->translator->getLocale();

		} else {
			$lang = 'cs';
		}

		$day = $date->format('N');

		if (isset($this->weekDayLocalized[$lang][$type][$day])) {
			$return = $this->weekDayLocalized[$lang][$type][$day];
			return ($ucfirst ? ucfirst($return) : strtolower($return));
		}

		return $date->format('D');
	}



	/**
	 * @param DateTime
	 * @param bool
	 * @return string
	 */
	public function month(DateTime $date, $ucfirst = TRUE)
	{
		if ($this->translator) {
			$lang = $this->translator->getLocale();

		} else {
			$lang = 'cs';
		}

		$month = $date->format('n');

		if (isset($this->monthLocalized[$lang][$month])) {
			$return = $this->monthLocalized[$lang][$month];
			return ($ucfirst ? ucfirst($return) : strtolower($return));
		}

		return $date->format('F');
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
	 * @param string
	 * @return string
	 */
	public static function term($from, $to, $type = 'full')
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

				if ($type == 'time') {
					return $timeFrom . ' - ' . $timeTo;
				}

			} else { // different day
				$term = $from->format('j. n.') . '-' . $to->format('j. n. Y') . ' ' . $timeFrom . '-' . $timeTo;
			}

		} else { // different year
			$term = $dayFrom . '  ' . $timeFrom . '-' . $dayTo . ' ' . $timeTo;
		}

		return $term;
	}

}
