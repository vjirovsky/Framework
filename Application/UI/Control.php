<?php

namespace Schmutzka\Application\UI;

use Nette;
use Nette\Utils\Strings;
use Schmutzka;

abstract class Control extends Nette\Application\UI\Control
{
	/** @inject @var Nette\Localization\ITranslator */
	public $translator;

	/** @inject @var Schmutzka\Templates\TemplateService */
	public $templateService;


	/**
	 * Automatic view setup
	 * @param  string
	 * @param  array
	 * @todo simulate as presenter render!
	 */
	public function __call($name, $args)
	{
		if ( ! method_exists($this, $name) && Strings::startsWith($name, "render")) {
			$view = lcfirst(substr($name, 6));

			// before
			$this->useTemplate($view);

			// call if exists

			// after
			$this->template->render();
		}
	}


	/**
	 * Create template and set file
	 * @param string
	 * @param bool
	 * @return Nette\Templating\FileTemplate
	 */
	public function createTemplate($class = NULL)
	{
		if ($this->templateService === NULL) {
			throw new \Exception("TemplateService is not available. Add component to config.");
		}

		$template = parent::createTemplate($class);
		$this->templateService->configure($template);

		if (! $template->getFile() && file_exists($this->getTemplateFilePath())) {
			$template->setFile($this->getTemplateFilePath());
		}

		return $template;
	}


	/**
	 * Sets up template
	 * @param string
	 */
	public function useTemplate($name = NULL)
	{
		$this->template->setFile($this->getTemplateFilePath($name));
	}


	/**
	 * Derives template path from class name
	 * @param string
	 * @return string
	 */
	protected function getTemplateFilePath($name = "")
	{
		$class = $this->getReflection();
		return dirname($class->getFileName()) . "/" . $class->getShortName() . ucfirst($name) . ".latte";
	}


	/**
	 * @param  string
	 * @return Nette\ComponentModel\IComponent
	 */
	protected function createComponent($name)
	{
		if ($component = parent::createComponent($name)) {
			return $component;

		} else {
			return $this->presenter->createComponent($name);
		}
	}

}
