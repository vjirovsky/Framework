<?php

namespace Schmutzka\Templating;

use Nette;
use Kdyby;


trait TTemplateSetup
{
	/** @inject @var Schmutzka\Templating\Helpers */
	public $helpers;

	/** @var callback[] */
	protected $helpersCallbacks = [];

	/** @var Nette\Localization\ITranslator */
	private $translator;


	public function injectTranslator(Nette\Localization\ITranslator $translator = NULL)
	{
		$this->translator = $translator;
	}


	/**
	 * @param  string|NULL
	 * @return Nette\Templating\FileTemplate;
	 */
	public function createTemplate($class = NULL)
	{
		$template = parent::createTemplate($class);

		// filters
		$template->registerFilter(new Nette\Templating\Filters\Haml);
		$template->registerFilter($engine = new Nette\Latte\Engine);

		// helpers
		$template->registerHelperLoader([$this->helpers, 'loader']);

		if (count($this->helpersCallbacks)) {
			foreach ($this->helpersCallbacks as $callback) {
				$template->registerHelperLoader($callback);
			}
		}

		// macros
		// Schmutzka\Templating\Macros::install

		// translation
		if ($this->translator) {
			$template->setTranslator($this->translator);

			if ($this->translator instanceof Kdyby\Translation\Translator) {
				Kdyby\Translation\Latte\TranslateMacros::install($engine->compiler);
				$template->registerHelperLoader([$this->translator->createTemplateHelpers(), 'loader']);
			}

		} else {
			$template->registerHelper('translate', function ($message) {
				return $message;
			});
		}

		return $template;
	}

}
