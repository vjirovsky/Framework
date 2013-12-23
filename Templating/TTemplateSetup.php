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

use Schmutzka;
use Nette;


trait TTemplateSetup
{
	/** @inject @var Schmutzka\Templating\Helpers */
	public $helpers;

	/** @var callback[] */
	protected $helpersCallbacks = [];


	/**
	 * @param  string|NULL
	 * @return  Nette\Templating\FileTemplate
	 */
	public function createTemplate($class = NULL)
	{
		$template = parent::createTemplate($class);

		// filters
		$template->registerFilter(new Nette\Templating\Filters\Haml);
		$template->registerFilter($engine = new Nette\Latte\Engine);

		// helpers
		$template->registerHelperLoader([$this->helpers, 'loader']);
		foreach ($this->helpersCallbacks as $callback) {
			$template->registerHelperLoader($callback);
		}

		// blank translations
		$template->registerHelper('translate', function ($message) {
			return $message;
		});

		// macros
		Schmutzka\Templating\Macros::install($engine->compiler);

		return $template;
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
