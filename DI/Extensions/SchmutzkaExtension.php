<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\DI\Extensions;

use Nette;
use Nette\PhpGenerator\ClassType;
use Schmutzka\DI\CompilerExtension;


class SchmutzkaExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$builder->getDefinition('user')
			->setClass('Schmutzka\Security\User')
			->setInject(TRUE);
		$this->parseFromFile(__DIR__ . '/config.neon');
	}


	public function beforeCompile()
	{
		$router = $this->getContainerBuilder()->getDefinition('router');
		foreach ($this->getSortedServicesByTag('routes') as $service) {
			$router->addSetup('offsetSet', array(NULL, '@' . $service));
		}
	}


	public function afterCompile(ClassType $class)
	{
		parent::afterCompile($class);
		$init = $class->methods['initialize'];
		$init->addBody('Schmutzka\DI\Extensions\FormExtension::register();');
	}

}
