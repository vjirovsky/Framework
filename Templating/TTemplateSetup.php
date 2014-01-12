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

use Kdyby;
use Nette;
use Schmutzka;


trait TTemplateSetup
{
	/** @inject @var Schmutzka\Templating\Helpers */
	public $helpers;

	/** @var Schmutzka\Templating\IHelpers */
	protected $appHelpers;


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
		if ($this->appHelpers) {
			$template->registerHelperLoader([$this->appHelpers, 'loader']);
		}

		// blank translations
		$template->registerHelper('translate', function ($message) {
			return $message;
		});

		// macros
		Schmutzka\Templating\Macros::install($engine->compiler);

		if (class_exists('Kdyby\Translation\Latte\TranslateMacros')) {
			Kdyby\Translation\Latte\TranslateMacros::install($engine->compiler);
		}

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
