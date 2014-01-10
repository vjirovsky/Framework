<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka;

use Nette;
use Nette\Utils\Strings;
use Schmutzka\Utils\Name;


class ParamService extends Nette\Object
{
	/** @var [] */
	public $parameters = [];


	/**
	 * @param array
	 */
	public function __construct($parameters)
	{
		$this->parameters = Nette\ArrayHash::from($parameters);
	}


	/**
	 * @param string
	 */
	public function &__get($name)
	{
		if ($name != 'parameters' && isset($this->parameters->{$name})) {
			return $this->parameters->{$name};
		}
	}


	/**
	 * @param  string
	 * @return  bool
	 */
	public function __isset($name)
	{
		if (isset($this->parameters[$name])) {
			return TRUE;
		}
	}


	/**
	 * @return string[]
	 */
	public function getModules()
	{
		$modules = [];
		foreach ($this->parameters as $key => $value) {
			if (Strings::endsWith($key, 'Module')) {
				$modules[] = substr($key, 0, -6);
			}
		}

		return $modules;
	}


	/**
	 * @param string
	 * @return array
	 */
	public function getModuleParameters($key)
	{
		if (Strings::contains($key, '\\')) {
			$key = Name::moduleFromNamespace($key, 'module');
		}

		$moduleName = $key . 'Module';
		if (isset($this->parameters->$moduleName)) {
			return $this->parameters->$moduleName;
		}

		return array();
	}

}
