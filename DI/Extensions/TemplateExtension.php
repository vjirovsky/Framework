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
use Nette\DI\CompilerExtension;


class TemplateExtension extends CompilerExtension
{
	/** @var string[] */
	private $defaults = [
		'filters' => [
			'haml' => 'Nette\Templating\Filters\Haml'
		],
		'helpers' => [
			'base' => 'Schmutzka\Templating\Helpers'
		],
		'macros' => [
			'Schmutzka\Templating\Macros'
		]
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$templateFactory = $builder->addDefinition($this->prefix('templateFactory'))
			->setClass('Schmutzka\Templating\TemplateFactory');

		foreach ($config['filters'] as $class) {
			$builder->addDefinition($prefixedName = $this->generateName($class))
				->setClass($class);
			$templateFactory->addSetup('$service->addFilter(?)', ['@' . $prefixedName]);
		}

		foreach ($config['helpers'] as $class) {
			$builder->addDefinition($prefixedName = $this->generateName($class))
				->setClass($class);

			$templateFactory->addSetup('$service->addHelperLoader(?)', ['@' . $prefixedName]);
		}

		foreach ($config['macros'] as $class) {
			$templateFactory->addSetup('$service->addMacroLoader(?)', [$class]);
		}
	}


	/**
	 * @param  string
	 * @return string
	 */
	private function generateName($class)
	{
		return $this->prefix(sha1($class));
	}

}
