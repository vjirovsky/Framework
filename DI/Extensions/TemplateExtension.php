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
use Nette\Utils\Strings;
use Schmutzka\DI\CompilerExtension;


class TemplateExtension extends CompilerExtension
{
	/** @var string[] */
	private $defaults = [
		'filters' => [],
		'helpers' => [],
		'macros' => []
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$templateFactory = $builder->addDefinition($this->prefix('templateFactory'))
			->setClass('Schmutzka\Templating\TemplateFactory');

		foreach ($config['filters'] as $service) {
			$templateFactory->addSetup('$service->addFilter(?)', [$service]);
		}

		foreach ($config['helpers'] as $service) {
			$templateFactory->addSetup('$service->addHelperLoader(?)', [$service]);
		}

		foreach ($config['macros'] as $name) {
			$templateFactory->addSetup('$service->addMacroLoader(?)', [$name]);
		}
	}


	public function beforeCompile()
	{
		$templateFactory = $this->getContainerBuilder()->getDefinition($this->prefix('templateFactory'));

		foreach ($this->getSortedServicesByTag('template.filter') as $name) {
			$templateFactory->addSetup('$service->addFilter(?)', ['@' . $name]);
		}

		foreach ($this->getSortedServicesByTag('template.helperLoader') as $name) {
			$templateFactory->addSetup('$service->addHelperLoader(?)', ['@' . $name]);
		}
	}

}
