<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Application\UI;

use Nette\Utils\Strings;
use WebLoader;


trait TCreateComponent
{
	/** @inject @var Nette\DI\Container */
	public $container;

	/** @inject @var WebLoader\LoaderFactory */
	public $webLoader;


	/**
	 * @param string
	 * @return Nette\ComponentModel\IComponent
	 */
	protected function createComponent($name)
	{
		$component = parent::createComponent($name);
		if ($component == NULL) {
			if (property_exists($this, $name)) {
				$component = $this->{$name}->create();

			} elseif ($this->isWebloaderControl($name)) {
				$component = $this->createWebloaderControl($name);
			}
		}

		$this->container->callInjects($component);
		$this->checkRequirements($component);

		return $component;
	}


	/**
	 * @param  string
	 * @return bool
	 */
	private function isWebloaderControl($name)
	{
		$detect = ['jsControl', 'cssControl'];
		foreach ($detect as $value) {
			if (Strings::endsWith($name, $value) || Strings::endsWith($name, ucfirst($value))) {
				return TRUE;
			}
		}

		return FALSE;
	}


	/**
	 * @param  string
	 * @return  WebLoader\Nette\CssLoader|WebLoader\Nette\JavaScriptLoader
	 */
	private function createWebloaderControl($name)
	{
		if (Strings::endsWith($name, 'ssControl')) {
			$part = ucfirst(substr($name, 0, -10)) ?: 'default';
			return $this->webLoader->createCssLoader($part);

		} else {
			$part = ucfirst(substr($name, 0, -9)) ?: 'default';
			return $this->webLoader->createJavascriptLoader($part);
		}
	}

}
