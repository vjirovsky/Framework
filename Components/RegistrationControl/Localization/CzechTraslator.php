<?php

namespace Schmutzka\Components\RegistrationControl\Localization;

use Schmutzka\Forms\Localization\SimpleTranslator;


class CzechTranslator extends SimpleTranslator
{
	/** @var string[] */
	protected $messages = [
		'components.registration.email' => 'Email',
		'components.registration.emailRuleFilled' => 'Zadejte email',
		'components.registration.alreadyExists' => 'Email nemá správný formát',
		'components.registration.password' => 'Heslo',
		'components.registration.passwordRuleFilled' => 'Zadejte heslo',
		'components.registration.send' => 'Registrovat se'
	];

}
