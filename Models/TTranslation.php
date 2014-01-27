<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Models;

use Models;
use Schmutzka\Utils\Name;


trait TTranslation
{
	/** @inject @var Nette\Localization\ITranslator */
	public $translator;


	/**
	 * @param  int|array
	 * @return NotORM_Row
	 */
	public function fetch($key = [])
	{
		$row = parent::fetch($key);

		// setup keys
		foreach ($this->localizedColumns as $value) {
			$row[$value];
		}

		return $row;
	}


	/**
	 * @param  array
	 */
	public function insert($values)
	{
		$data = $this->extractLocalizedData($values);

		// normal insert
		if ( ! isset($values) || $values == FALSE) {
			$values['id'] = NULL;
		}

		$table = $this->getTableName();
		$data[$table . '_id'] = $id = parent::insert($values);
		$this->db->{$table . '_translation'}->insert_update($data, $data);

		return $id;
	}


	/**
	 * @param  array
	 * @param  int
	 */
	public function update($values, $id)
	{
		$data = $this->extractLocalizedData($values);

		// normal update
		if (isset($values)) {
			parent::update($values, $id);
		}

		$table = $this->getTableName();
		$data[$table . '_id'] = $id;
		$this->db->{$table . '_translation'}->insert_update($data, $data);
	}


	/**
	 * @param  array
	 * @return array [ id => name ]
	 */
	public function fetchList($key = [])
	{
		$result = $this->fetchAll($key);

		$list = [];
		foreach ($result as $id => $row) {
			$list[$id] = $row['name'];
		}

		return $list;
	}


	/**
	 * @param  array
	 * @return  array
	 */
	private function extractLocalizedData(&$values)
	{
		$data = [];
		foreach ($this->localizedColumns as $key) {
			if (isset($values[$key])) {
				$data[$key] = $values[$key];
				unset($values[$key]);
			}
		}

		$data['language_id'] = $this->translator->getLocale();

		return $data;
	}


	/**
	 * @return  string
	 */
	private function getTableName()
	{
		return Name::tableFromClass(get_class($this));
	}

}
