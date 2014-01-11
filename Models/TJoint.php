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


trait TJoint
{
	/** @var string */
	private $mainTable;

	/** @var string */
	private $otherTable;

	/** @var string */
	private $mainKey;

	/** @var string */
	private $otherKey;


	public function __construct()
	{
		$this->setup();
 	}


	/**
	 * @param  int
	 * @param  string|NULL
	 * @return  array
	 */
	public function fetchByMain($id, $secondKey = NULL)
	{
		return $this->table($this->mainKey, $id)
			->fetchPairs($this->otherKey, $secondKey ?: $this->otherKey);
	}



	/**
	 * @param  int
	 * @return  NotORM_Result
	 */
	public function fetchResultByMain($id)
	{
		return $this->fetchResultHelper($id, $this->mainKey, $this->otherKey, $this->otherTable);
	}


	/**
	 * @param  int
	 * @return  NotORM_Result
	 */
	public function fetchResultByOther($id)
	{
		return $this->fetchResultHelper($id, $this->otherKey, $this->mainKey, $this->mainTable);
	}


	/**
	 * Update current data - remove old, add new
	 * @param  int
	 * @param  array
	 */
	public function modify($id, $data)
	{
		$oldItems = $this->table($this->mainKey, $id)
			->fetchPairs($this->otherKey, $this->otherKey);

		$key[$this->mainKey] = $id;

		foreach ($data as $otherKey) {
			$key[$this->otherKey] = $otherKey;
			if ( ! isset($oldItems[$otherKey])) {
				$this->insert($key);
			}

			unset($oldItems[$otherKey]);
		}

		foreach ($oldItems as $otherKey) {
			$key[$this->otherKey] = $otherKey;
			$this->delete($key);
		}
	}


	/**
	 * Update current data - remove old, add new
	 * @param  int
	 * @param  array
	 */
	public function modifyArrayData($id, $data)
	{
		$oldItemsIds = $this->table($this->mainKey, $id)
			->fetchPairs('id', 'id');

		$checkKey[$this->mainKey] = $id;

		foreach ($data as $key => $value) {
			$checkKey[$this->otherKey] = $key;

			if ($remove = $this->table($checkKey)->fetch()) {
				unset($oldItemsIds[$remove['id']]);
				$this->update($value, $remove['id']);

			} else {
				$value[$this->mainKey] = $id;
				$this->insert($value);
			}
		}

		foreach ($oldItemsIds as $key) {
			$this->delete($key);
		}
	}


	private function setup()
	{
		$data = explode('\\', get_class());
		$data = explode('In', $data[1]); // what if start with In

		$this->mainTable = $this->camelCaseToUnderscore($data[1]);
		$this->otherTable = $this->camelCaseToUnderscore($data[0]);
		$this->mainKey = $this->mainTable . '_id';
		$this->otherKey = $this->otherTable . '_id';
	}


	/**
	 * @param  string
	 * @return  string
	 */
	private function camelCaseToUnderscore($string)
	{
    	$string[0] = strtolower($string[0]);
    	$func = create_function('$c', 'return "_" . strtolower($c[1]);');
    	return preg_replace_callback('/([A-Z])/', $func, $string);
    }


	/**
	 * @param  int
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return  NotORM_Result
	 */
	private function fetchResultHelper($cond, $condKey, $key, $table)
	{
		$keys = $this->table($condKey, $cond)
			->fetchPairs($key, $key);

		$result = $this->db->{$table}
			->where('id', $keys)
			->order('name'); // @ ok?

		return $result;
	}

}
