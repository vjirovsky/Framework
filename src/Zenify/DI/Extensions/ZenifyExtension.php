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

		$config = $this->loadFromFile(__DIR__ . '/config.neon');
		$this->compiler->parseServices($builder, $config);
	}


	public function beforeCompile()
	{
		$builder = $this->containerBuilder;
		$router = $builder->getDefinition('router');

		$builder->addDefinition($this->prefix('router'))
			->setClass('App\RouterFactory');

		$builder->addDefinition($this->prefix('routerFactory'))
			->setFactory('@\App\RouterFactory::create')
			->setAutowired(FALSE);

		foreach ($this->compiler->getExtensions('Zenify\DI\Providers\IRouterProvider') as $name => $extension) {
			$service = $this->prefix($name);
			$builder->addDefinition($service)
				->setClass($extension->reflection->name)
				->setAutowired(FALSE);

			$factory = $this->prefix('factory.' . $name);
			$builder->addDefinition($factory)
				->setFactory('@' . $service . '::getRoutes')
				->setAutowired(FALSE);

			$router->addSetup('offsetSet', array(NULL, '@' . $factory));
		}
	}


	public function afterCompile(ClassType $class)
	{
		parent::afterCompile($class);
		$init = $class->methods['initialize'];
		$init->addBody('Zenify\DI\Extensions\FormExtension::register();');
	}

}
