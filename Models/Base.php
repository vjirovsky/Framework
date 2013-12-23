<?php

namespace Schmutzka\Models;

use Nette;
use NotORM;
use Schmutzka\Utils\Name;


abstract class Base extends Nette\Object
{
	/** @inject @var NotORM */
	public $db;

	/** @var string */
	private $tableName;


	public function table()
	{
		$args = func_get_args();
		if (count($args) == 1 && is_numeric($args[0])) {
			array_unshift($args, 'id');
		}

		return call_user_func_array(array($this->db, $this->getTableName()), $args);
	}


	/********************** basic operations **********************/


	/**
	 * @param  array
	 * @return  NotORM_Result
	 */
	public function fetchAll($key = [])
	{
		if ($key) {
			return $this->table($key);

		} else {
			return $this->table();
		}
	}


	/**
	 * @param array
	 * @return NotORM_Row
	 */
	public function insert($array)
	{
		return $this->table()
			->insert($array);
	}


	/**
	 * @param  int|array
	 * @return NotORM_Row|NULL
	 */
	public function fetch($key)
	{
		return $this->table(is_numeric($key) ? ['id' => $key] : $key)
			->fetch();
	}


	/**
	 * @param  string
	 * @param  array
	 * @return NotORM_Row
	 */
	public function fetchLast($orderBy, $key = [])
	{
		return $this->table($key)
			->order($orderBy . ' DESC')
			->limit(1)
			->fetch();
	}


	/**
	 * @param array
	 * @param array
	 * @return NotORM_Row
	 */
	public function update($array, $key)
	{
		$this->table($key)
			->update($array);

		return $this->fetch($key);
	}


	/**
	 * @param mixed
	 * @param array
	 */
	public function duplicate($key, $change = [])
	{
		if (is_array($key)) {
			$result = $this->fetchAll($key);

		} else {
			$row = $this->fetch($key);
			unset($row['id']);
			return $this->insert($row);
		}

		foreach ($result as $row) {
			unset($row['id']);
			if ($change) {
				foreach ($change as $keyChange => $valueChange) {
					if (isset($row[$keyChange])) {
						$row[$keyChange] = $valueChange;
					}
				}
			}

			$this->insert($row);
		}
	}


	/**
	 * @param mixed
	 * @param int
	 */
	public function delete($key)
	{
		return $this->table($key)
			->delete();
	}


	/**
	 * @param array
	 * @return int
	 */
	public function count($key = [])
	{
		return $this->table($key)
			->count();
	}


	/**
	 * @param string
	 * @param string
	 * @param array
	 * @return array
	 */
	public function fetchPairs($id = 'id', $column = NULL, $key = [])
	{
		return $this->table($key)
			->order($column)
			->fetchPairs($id, $column);
	}


	/**
	 * Fetch list shortcut
	 * @param array
	 * @return array
	 */
	public function fetchList($key = [])
	{
		return $this->fetchPairs('id', 'name', $key);
	}


	/**
	 * Get list by user id
	 * @param  int
	 * @return  array
	 */
	public function fetchListByUser($userId)
	{
		return $this->table(['user_id' => $userId])
			->fetchPairs('id', 'name');
	}


	/**
	 * Fetch random table row
	 * @param  array
	 * @return NotORM_Row
	 */
	public function fetchRandom($key = [])
	{
		return $this->table($key)
			->order('RAND()')
			->limit(1)
			->fetch();
	}


	/**
	 * @param  string
	 * @return NotORM_Row
	 */
	public function fetchByUid($uid)
	{
		return $this->fetch(['uid' => $uid]);
	}


	/**
	 * Insert, update on duplicate key
	 * @param array
	 * @param mixed
	 */
	public function upsert($data, $unique)
	{
		if ( ! is_array($unique)) {
			if ( ! $unique) {
				return $this->table()
					->insert($data);
			}

			$unique = ['id' => $unique];
		}

		return $this->table()
			->insert_update($unique, $data, $data);
	}


	/**
	 * @return string
	 */
	private function getTableName()
	{
		if ($this->tableName == NULL) {
			$this->tableName = Name::tableFromClass(get_class($this));
		}

		return $this->tableName;
	}

}
