<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Models\Entity;



trait TTranslation
{
	/** @var string */
	public static $lang;


	/**
	 * @param  string
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		if ( ! isset($this->row[$offset])) {
			$table = $this->result->table . '_translation';

			$row = $this->$table('language_id', array(self::$lang, 'cs'))->order("language_id = '" . self::$lang . "' DESC")
				->limit(1)
				->fetch();

			if ($row) {
				foreach ($row as $key => $val) {
					$this->row[$key] = $val;
				}

			} else {
				$this->row[$offset] = NULL;
			}
		}

		return parent::offsetExists($offset);
	}


	/**
	 * @param  string
	 * @return bool
	 */
	public function offsetGet($offset)
	{
		$this->offsetExists($offset);
		return parent::offsetGet($offset);
	}
}
