<?php

namespace Schmutzka\Models;


trait TTree
{
	/** @var array */
	public $structure;

	/** @var string key column name */
	protected $idColumn = 'id';

	/** @var string parent key column name */
	protected $parentColumn = 'parent_id';


	/**
	 * @param array
	 * @return array { [ id => { [ NotORM_Row => ..., children ] } ] }
	 */
	public function fetchStructure($cond = [])
	{
		$data = $this->fetchData($cond);
		$structure = [];

		foreach ($data as $row) {
			if (!$row[$this->parentColumn]) { // no parent items
				$structure[$row[$this->idColumn]] = $row;
			}
		}

		foreach ($structure as &$row) {
			$row['children'] = $this->getChildren($cond, $row[$this->idColumn]);
		}

		return $structure;
	}


	/**
	 * Convert siple structure to fullroad pair lsit
	 * @param string
	 * @return array
	 * from: 5 => Current category
	 * to: 5 => Main \ Subcategory \ Current category
	 */
	public function fullroadView($sep = ' » ')
	{
		$data = $this->fetchData()->fetchPairs('id');
		$array = [];

		foreach ($data as $key => $row) {
			$item = $row[$this->idColumn];
			$above = $data[$key][$this->parentColumn];

			while(isset($above)) {
				$item = $data[$above][$this->idColumn] . $sep . $item;
				$above = $data[$above][$this->parentColumn];
			}

			$array[$key] = $item;
		}

		return $array;
	}


	/**
	 * Returns children by record id
	 * @param array
	 * @param int
	 * @return array
	 */
	private function getChildren($cond, $parentId)
	{
		$data = $this->fetchData($cond);
		$array = [];

		foreach ($data as $row) {
			if ($row[$this->parentColumn] == $parentId) {
				if (!is_array($row)) {  // result from database
					$row = iterator_to_array($row);
				}

				$array[$row[$this->idColumn]] = $row;
			}
		}

		foreach ($array as $key => $row) {
			$row['children'] = self::getChildren($cond, $key);
		}

		if (count($array)) {
			return $array;
		}

		return NULL;
	}

}
