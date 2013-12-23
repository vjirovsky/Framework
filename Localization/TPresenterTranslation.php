<?php

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
		if ($this->sessionResolver->getLocale() == NULL) {
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

		// automatically ad to translator?
		list(, $presenterName) = explode(':', $this->presenter->name);
		$this->template->trD = strtolower($presenterName . '.' . $this->presenter->view);
	}

}
