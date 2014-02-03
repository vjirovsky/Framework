<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\DI;

use Nette;


class CompilerExtension extends Nette\DI\CompilerExtension
{

	/**
	 * @param  string
	 */
	public function parseFromFile($file)
	{
		$builder = $this->getContainerBuilder();
		$this->compiler->parseServices($builder, $this->loadFromFile($file));
	}


	/**
	 * @param string
	 * @return []
	 */
	protected function getSortedServicesByTag($tag)
	{
		$container = $this->getContainerBuilder();

		$services = [];
		foreach ($container->findByTag($tag) as $def => $meta) {
			$priority = isset($meta['priority']) ? $meta['priority'] : (int) $meta;
			$services[$priority][] = $def;
		}

		krsort($services);

		return Nette\Utils\Arrays::flatten($services);
	}


	/**
	 * @param int
	 */
	protected function addRouter($priority = 100)
	{
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('routerFactory'))
			->setFactory(ucfirst($this->name) . '\RouterFactory');

		$builder->addDefinition($this->prefix('router'))
			->setFactory('@' . $this->name . '.routerFactory::create')
			->setAutowired(FALSE)
			->addTag('routes', ['priority' => $priority]);
	}

}
