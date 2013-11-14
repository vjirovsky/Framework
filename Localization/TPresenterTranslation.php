<?php

namespace Schmutzka\Localization;

use Kdyby;
use Nette;


trait TTemplateTranslation
{
	/** @persistent @var string */
	public $locale;

	/** @inject @var Kdyby\Translation\Translator */
	public $translator;


	/**
	 * @param  string|NULL
	 * @return  Nette\Templating\FileTemplate
	 */
	public function createTemplate($class = NULL)
	{
		$template = parent::createTemplate($class);
		$template->setTranslator($this->translator);

		$engine = new Nette\Latte\Engine;
		Kdyby\Translation\Latte\TranslateMacros::install($engine->compiler);
		$template->registerHelperLoader([$this->translator->createTemplateHelpers(), 'loader']);

		return $template;
	}

}
