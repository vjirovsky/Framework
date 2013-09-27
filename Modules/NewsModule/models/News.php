<?php

namespace Schmutzka\Models;

use Schmutzka;


class News extends BaseTranslation
{
	/** @var array */
	public $localizedColumns = ['title', 'text'];


	/**
	 * @param  int
	 * @return NotORM_Result
	 */
	public function fetchFront($limit = NULL)
	{
		return $this->fetchAll()->order('created DESC')
			->limit($limit);
	}

}
