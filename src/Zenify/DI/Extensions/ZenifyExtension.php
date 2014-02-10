<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\DI\Extensions;

use Nette;
use Nette\PhpGenerator\ClassType;
use Zenify\DI\CompilerExtension;


class ZenifyExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition('user')
			->setClass('Zenify\Security\User')
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
		$init->addBody('Zenify\DI\Extensions\FormExtension::register();');
	}

}
