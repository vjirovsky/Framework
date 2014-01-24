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
	/** @inject @var Nette\Localization\ITranslator */
	public $translator;


	/**
	 * @param  string
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		$lang = $this->translator->getLocale();

		if ( ! isset($this->row[$offset])) {
			list (, $table) = explode('\\', __CLASS__);
			$table = lcfirst($table) . '_translation';

			$row = $this->row->$table()->where('language_id', [$lang, 'cs', 'en'])
				->order("language_id = '" . $lang . "' DESC")
				->limit(1)
				->fetch();

			if ($row) {
				foreach ($row as $key => $value) {
					$this->row[$key] = $value;
				}

			} else {
				$this->row[$offset] = NULL;
			}
		}

		return $this->row[$offset];
	}


	/**
	 * @param  string
	 * @return bool
	 */
	public function offsetGet($offset)
	{
		$this->offsetExists($offset);
		return $this->row[$offset];
	}

}
