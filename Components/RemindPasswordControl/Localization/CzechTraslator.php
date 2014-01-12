<?php

namespace Schmutzka\Components\RemindPasswordControl\Localization;

use Schmutzka\Forms\Localization\SimpleTranslator;


class CzechTranslator extends SimpleTranslator
{
	/** @var string[] */
	protected $messages = [
		'components.remindPassword.email' => 'Email',
		'components.remindPassword.emailFilledRule' => 'Zadejte email',
		'components.remindPassword.emailFormatRule' => 'Email nemá správný formát',
		'components.remindPassword.send' => 'Zaslat nové heslo',
		'components.remindPassword.newPasswordSetUp' => 'Nové heslo zasláno',
		'components.remindPassword.userNotExist' => 'Tento uživatel neexistuje',
	];

}
