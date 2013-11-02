<?php

namespace Schmutzka\Localization;


trait PresenterTranslationTrait
{
	/** @persistent @var string */
	public $lang;

	/** @var Nette\localization\ITranslator */
	protected $translator;


	public function injectTranslator(Nette\Localization\ITranslator $translator = NULL)
	{
		$this->translator = $translator;
	}

}
