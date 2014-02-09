<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify;

use Nette;


class DateTime extends Nette\DateTime
{
	/** @var string[] */
	private static $holidays = ['12-24', '12-25', '12-26', '01-01', '05-01', '05-08', '07-05', '07-06', '09-28', '10-28', '11-17'];


	/**
	 * @return bool
	 */
	public function isToday()
	{
		return ($this->format('Y-m-d') == self::from(NULL)->format('Y-m-d'));
	}


	/**
	 * @return bool
	 */
	public function isWorkingDay()
	{
		if ($this->format('N') >= 6 || $this->isHoliday()) {
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * @return bool
	 */
	public function isWeekend()
	{
		return ($this->format('N') >= 6);
	}


	/**
	 * @return bool
	 */
	public function isHoliday()
	{
		if (in_array($this->format('m-d'), self::$holidays)) {
			return TRUE;
		}

		if ($this->format('m-d') == strftime('%m-%d', easter_date($this->format('Y')))) { // easter
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * @return bool
	 */
	public function isPast()
	{
		if ($this < new self) {
			return TRUE;
		}

		return FALSE;
	}

}
