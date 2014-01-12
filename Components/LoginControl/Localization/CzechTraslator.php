<?php

namespace Schmutzka\Components\LoginControl\Localization;

use Schmutzka\Forms\Localization\SimpleTranslator;


class CzechTranslator extends SimpleTranslator
{
	/** @var string[] */
	protected $messages = [
		'components.login.email' => 'Email',
		'components.login.emailFilledRule' => 'Zadejte email',
		'components.login.emailFormatRule' => 'Email nemá správný formát',
		'components.login.password' => 'Heslo',
		'components.login.passwordFilledRule' => 'Zadejte heslo',
		'components.login.send' => 'Přihlásit se'
	];

}
