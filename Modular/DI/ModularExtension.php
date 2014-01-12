<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Modular\DI;

use Nette;


class ModularExtension extends Nette\DI\CompilerExtension
{

	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();
		$router = $container->getDefinition('router');

		foreach ($this->compiler->getExtensions() as $extension) {
			if ($extension instanceof IRouterProvider) {
				// foreach ($extension->getRouters() as $service) {
				foreach ($extension->getRouters() as $parameters) {
					// $router->addSetup('offsetSet', array(NULL, $service));
					$router->addSetup('$service[] = new Nette\Application\Routers\Route(?, ?)', $parameters);
				}
			}
		}

		return $this->getConfig();
	}

}
