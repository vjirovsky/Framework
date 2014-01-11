<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Localization;

use Kdyby;
use Nette;


trait TTemplateTranslation
{
	/** @inject @var Kdyby\Translation\Translator */
	public $translator;

	/** @inject @var Kdyby\Translation\LocaleResolver\SessionResolver */
	public $sessionResolver;


	/**
	 * @param string
	 */
	public function handleSetLocale($locale)
	{
		$this->sessionResolver->setLocale($locale);
		$this->redirect('this');
	}


	public function setupTranslator()
	{
		if ($this->translator->getLocale() == NULL) {
			foreach ($this->translator->getAvailableLocales() as $locale) {
				if (in_array($locale, ['cs', 'en'])) {
					$this->sessionResolver->setLocale($locale);
					break;
				}
			}
		}

		$this->template->setTranslator($this->translator);

		$engine = new Nette\Latte\Engine;
		Kdyby\Translation\Latte\TranslateMacros::install($engine->compiler);

		$this->template->registerHelperLoader([$this->translator->createTemplateHelpers(), 'loader']);
	}

}
