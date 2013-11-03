<?php

namespace Schmutzka\Application\UI;

use Nette\Utils\Strings;
use WebLoader;


trait TCreateComponent
{

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

			} elseif (method_exists($this->context, 'createService' .  ucfirst($name))) { // @deprecated
				$component = call_user_func(array($this->context, 'createService' .  ucfirst($name)));
			}
		}

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
		$webtemp = $this->template->basePath . '/webtemp/';

		if (Strings::endsWith($name, 'ssControl')) {
			$part = ucfirst(substr($name, 0, -10)) ?: 'Default';
			$compiler = $this->context->{'webloader.css' . $part . 'Compiler'};

			return new WebLoader\Nette\CssLoader($compiler, $webtemp);

		} else {
			$part = ucfirst(substr($name, 0, -9)) ?: 'Default';
			$compiler = $this->context->{'webloader.js' . $part . 'Compiler'};

			return new WebLoader\Nette\JavaScriptLoader($compiler, $webtemp);
		}
	}

}
