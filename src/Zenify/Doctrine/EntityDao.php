<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Doctrine;

use Kdyby;


class EntityDao extends Kdyby\Doctrine\EntityDao
{

	/**
	 * @param  string
	 * @param  strin[]
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		if ( ! method_exists($this, $name) && method_exists($this->dao, $name)) {
			return call_user_func_array(array($this->dao, $name), $args);
		}
	}

}
