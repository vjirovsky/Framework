<?php

namespace Schmutzka\NotORM;

// fwlicence -> tab
// inspire https://github.com/Kdyby/Doctrine/blob/master/src/Kdyby/Doctrine/ResultSet.php

class ResultSet implements ArrayIterator
{

	private $result;


	public function __construct($result)
	{
		$this->result = $result;
	}


	/**
	 * @return array
	 */
	public function getIterator()
	{
		return $this->coll;
	}


	public function get($entity)
	{
		return $this->result[$entity];
	}


	/**
     * @param string
     * @return boolean
     */
    public function has($entity)
    {
        return $this->result->offsetExists($entity);
    }

}
