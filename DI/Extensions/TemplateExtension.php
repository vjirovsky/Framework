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
use Nette\Utils\Validators;
use Schmutzka\DI\CompilerExtension;


class TemplateExtension extends CompilerExtension
{
	const FILTER_TAG = 'template.filter';
	const HELPER_TAG = 'template.helper';
	const HELPER_LOADER_TAG = 'template.helperLoader';


	/** @var string[] */
	private $defaults = [
		'filters' => [],
		'helperLoaders' => [],
		'helpers' => [],
		'macroSets' => []
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$templateFactory = $builder->addDefinition($this->prefix('templateFactory'))
			->setClass('Schmutzka\Templating\TemplateFactory');

		foreach ($config['filters'] as $service) {
			$templateFactory->addSetup('addFilter', [$service]);
		}

		foreach ($config['helperLoaders'] as $service) {
			$templateFactory->addSetup('addHelperLoader', [$service]);
		}

		foreach ($config['helpers'] as $name => $callback) {
			$templateFactory->addSetup('addHelper', [$name, $callback]);
		}
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$templateFactory = $builder->getDefinition($this->prefix('templateFactory'));

		foreach (array_keys($builder->findByTag(self::FILTER_TAG)) as $serviceName) {
			$templateFactory->addSetup('addFilter', ['@' . $serviceName]);
		}

		foreach (array_keys($builder->findByTag(self::HELPER_LOADER_TAG)) as $serviceName) {
			$templateFactory->addSetup('addHelperLoader', ['@' . $serviceName]);
		}

		foreach (array_keys($builder->findByTag(self::HELPER_TAG)) as $serviceName) {
			$service = $builder->getDefinition($serviceName);
			$tags = $service->tags;
			if (isset($tags['helpers'])) {
				foreach ($tags['helpers'] as $name => $method) {
					if ( ! is_string($name)) {
						$name = $method;
					}

					$templateFactory->addSetup('addHelper', [$name, ['@' . $serviceName, $method]]);
				}
			}
		}
	}

}
