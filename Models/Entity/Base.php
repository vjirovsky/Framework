<?php

namespace Schmutzka\Models\Entity;

use ArrayAccess;
use NotORM_Row;
use Nette;


// abstract class Base extends NotORM_Row
abstract class Base extends Nette\Object implements ArrayAccess
{
	/** @var NotORM_Row */
	public $row;


	public function __construct($row)
	{
		$this->row = $row;
	}


	public function offsetGet($key)
	{
		$functionName = 'get' . $this->getFunctionName($key);
		if (method_exists($this, $functionName)) {
			return $this->$functionName();
		}

		$this->row[$key];
	}


	public function offsetSet($key, $value)
	{
		$this->row[$key] = $value;
	}


	public function offsetUnset($key)
	{
		unset($this->row[$key]);
	}


	/**
	 * @param string
	 * @return string
	 */
	private function getFunctionName($key)
	{
		dd(__CLASS__ . ' - move to Name');
		$key[0] = strtoupper($key[0]);
		$func = create_function('$c', 'return strtoupper($c[1]);');
		return preg_replace_callback('/_([a-z])/', $func, $key);
	}


	/**
	 * @return []
	 */
	public function toArray()
	{
		return $this->row->toArray();
	}

}
