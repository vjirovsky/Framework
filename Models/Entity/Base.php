<?php

namespace Schmutzka\Models\Entity;

use NotORM_Row;


abstract class Base extends NotORM_Row
{

	public function offsetGet($key)
	{
		$functionName = 'get' . $this->getFunctionName($key);
		if (method_exists($this, $functionName)) {
			return $this->$functionName();
		}

		return parent::offsetGet($key);
	}


	public function offsetSet($key, $value)
	{
		$functionName = 'set' . $this->getFunctionName($key);
		if (method_exists($this, $functionName)) {
			$this->$functionName($value);

		} else {
			parent::offsetSet($key, $value);
		}
	}


	public function getRaw($key)
	{
		return parent::offsetGet($key);
	}


	public function setRaw($key, $value)
	{
		parent::offsetSet($key, $value);
	}


	/**
	 * @param string
	 * @return string
	 */
	private function getFunctionName($key)
	{
		$key[0] = strtoupper($key[0]);
		$func = create_function('$c', 'return strtoupper($c[1]);');
		return preg_replace_callback('/_([a-z])/', $func, $key);
	}


	/**
	 * @return NotORM
	 */
	public function getDb()
	{
		return $this->result->notORM;
	}

}
