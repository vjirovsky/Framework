<?php

// inspire https://github.com/Kdyby/Doctrine/blob/master/src/Kdyby/Doctrine/Entities/BaseEntity.php

namespace Schmutzka\Models\Entity;

use ArrayAccess;
use NotORM_Row;
use Nette;


abstract class Base extends Nette\Object implements \ArrayAccess, \Serializable
{
	/** @var NotORM_Row */
	public $row;

	/** @inject @var Nette\DI\Container */
	public $container;


	public function __construct($row)
	{
		$this->row = $row;
	}


	public function &__get($name)
	{
		if (isset($this->row[$name . '_id'])) {
			if ($data = $this->row->$name) {
				if ($data instanceof \NotORM_Row) {
					$entity = $this->createEntity($data, $name);
					return $entity;
				}
			}
		}

		return $this->row[$name];
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


	public function serialize()
	{
		return serialize($this->row);
	}


	public function unserialize($row)
	{
		$this->row = unserialize($row);
	}


	/**
	 * @param  NotORM_Row
	 * @param  string
	 * @return Entity|NotORM_Row
	 */
	public function createEntity($row, $name)
	{
		$class = 'Entity\\' . ucfirst($name);
		if (class_exists($class)) {
			$entity = new $class($row);
			$this->container->callInjects($entity);
			return $entity;
		}

		return $row;
	}

}
