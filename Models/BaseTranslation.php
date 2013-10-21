<?php

namespace Schmutzka\Models;

use GettextTranslator;
use NotORM_Row_Lang;
use Schmutzka\Utils\Name;


abstract class BaseTranslation extends Base
{
	/** @inject @var Nette\Localization\ITranslator */
	public $translator;


	/**
	 * @param  int|array
	 * @return NotORM_Row
	 */
	public function fetch($key)
	{
		NotORM_Row_Lang::$lang = $this->translator->getLang();

		$row = parent::fetch($key);

		// setup keys
		foreach ($this->localizedColumns as $value) {
			$row[$value];
		}

		return $row;
	}


	/**
	 * @param  array
	 * @return NotORM_Result
	 */
	public function fetchAll($key = [])
	{
		NotORM_Row_Lang::$lang = $this->translator->getLang();
		return parent::fetchAll($key);
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

		$data['language_id'] = $this->translator->getLang();

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
