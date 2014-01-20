<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Components\LoginControl\Localization;

use Schmutzka\Forms\Localization\SimpleTranslator;


class CzechTranslator extends SimpleTranslator
{
	/** @var string[] */
	protected $messages = [
		'components.email' => 'Email',
		'components.emailFilledRule' => 'Zadejte email',
		'components.emailFormatRule' => 'Email nemá správný formát',
		'components.password' => 'Heslo',
		'components.passwordFilledRule' => 'Zadejte heslo',
		'components.login.send' => 'Přihlásit se'
	];

}
