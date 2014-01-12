<?php

namespace Schmutzka\Forms\Localization;

use Nette;


class SimpleTranslator implements Nette\Localization\ITranslator
{
	/** @var string[] */
	protected $messages;


	/**
	 * @param  string
	 * @param  int
	 * @return string
	 */
	public function translate($message, $count = NULL)
	{
		if (isset($this->messages[$message])) {
			return $this->messages[$message];
		}

		return $message;
	}
}
