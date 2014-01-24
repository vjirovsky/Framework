<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Templating;

use Nette;


trait TTemplateFactory
{
	/** @inject @var Schmutzka\Templating\ITemplateFactory */
	public $templateFactory;


	/**
	 * @param  string
	 * @return  Nette\Templating\Template
	 */
	public function createTemplate($class = NULL)
	{
		return $this->templateFactory ? $this->templateFactory->createTemplate($this, $class) : parent::createTemplate($class);
	}


	/**
	 * @return string[]
	 */
	public function formatLayoutTemplateFiles()
	{
		$layout = ($this->layout ?: 'layout') . '.latte';

		$layoutTemplateFiles = parent::formatLayoutTemplateFiles();
		$layoutTemplateFiles[] = $this->paramService->appDir . '/AdminModule/templates/@' . $layout;
		$layoutTemplateFiles[] = $this->paramService->appDir . '/FrontModule/templates/@' . $layout;

		return $layoutTemplateFiles;
	}

}
