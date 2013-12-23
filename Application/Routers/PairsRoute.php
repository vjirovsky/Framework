<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Application\Routers;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Utils\Strings;


class PairsRoute extends Route
{
	use TIdRoute;


	/**
	 * @param string
	 * @param array
	 * @param Models\Base
	 * @param Nette\Caching\Cache
	 * @param string
	 * @param string
	 */
	public function __construct($mask, $metadata = [], $model, Nette\Caching\Cache $cache, $keyName = 'id', $valueName = 'name')
	{
		$this->mask = $mask;
		$this->metadata = $metadata;
		$this->model = $model;
		$this->cache = $cache;
		$this->keyName = $keyName;
		$this->valueName = $valueName;

		parent::__construct($mask, $metadata);
	}


	/**
	 * @return  array [key => value]
	 */
	public function buildTranslateTable()
	{
		$result = $this->model->fetchAll();

		$data = [];
		foreach ($result as $key => $row) {
			$key = $row[$this->keyName];
			$value = Strings::webalize($row[$this->valueName]);
			$data[$key] = $value;
		}

		return $data;
	}

}
