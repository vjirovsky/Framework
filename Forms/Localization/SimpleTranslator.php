<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Forms\Localization;

use Nette;


class SimpleTranslator implements Nette\Localization\ITranslator
{
	/** @var string[] */
	private $defaultMessages = [
		'components.email' => 'Email',
		'components.emailFilledRule' => 'Zadejte email',
		'components.emailFormatRule' => 'Email nemá správný formát',
		'components.password' => 'Heslo',
		'components.passwordFilledRule' => 'Zadejte heslo',
	];

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

		} elseif (isset($this->defaultMessages[$message])) {
			return $this->defaultMessages[$message];
		}

		return $message;
	}
}
