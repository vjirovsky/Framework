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


	public function setupTranslator()
	{
		$this->template->setTranslator($this->translator);

		$engine = new Nette\Latte\Engine;
		Kdyby\Translation\Latte\TranslateMacros::install($engine->compiler);

		$this->template->registerHelperLoader([$this->translator->createTemplateHelpers(), 'loader']);
	}

}
