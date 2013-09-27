<?php

namespace Schmutzka\Models;

use GettextTranslator;
use NotORM_Row_Lang;


abstract class BaseTranslation extends Base
{
	/** @var protected */
	protected $localizedColumns = ['name'];


	public function inject(GettextTranslator\Gettext $translator)
	{
		d($translator->getLang(), 'hewmmm');
		d(NotORM_Row_Lang::$lang = $translator->getLang());
	}


	/**
	 * @param  int|array
	 * @return NotORM_Row
	 */
	public function fetch($key)
	{
		NotORM_Row_Lang::$lang = $translator->getLang();

		$row = parent::fetch($key);

		// setup keys
		foreach ($this->localizedColumns as $value) {
			$row[$value];
		}

		return $row;
	}


	public function update($values, $key)
	{

	}

}
