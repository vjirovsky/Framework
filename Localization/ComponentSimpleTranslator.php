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

use Nette\Localization\ITranslator;


class ComponentSimpleTranslator implements ITranslator
{
	/** @var string[] */
	private $messages = [
		'components.changePassword.oldPassword' => 'Staré heslo',
		'components.changePassword.oldPasswordFilledRule' => 'Zadejte staré heslo',
		'components.changePassword.newPassword' => 'Nové heslo',
		'components.changePassword.newPasswordFilledRule' => 'Zadejte nové heslo',
		'components.changePassword.send' => 'Změnit heslo'
		'components.email' => 'Email',
		'components.emailFilledRule' => 'Zadejte email',
		'components.emailFormatRule' => 'Email nemá správný formát',
		'components.login.send' => 'Přihlásit se'
		'components.password' => 'Heslo',
		'components.passwordFilledRule' => 'Zadejte heslo',
		'components.registration.alreadyExists' => 'Tento email je již registrován. Zvolte jiný.'
		'components.remindPassword.send' => 'Zaslat nové heslo',
		'components.remindPassword.newPasswordSetUp' => 'Nové heslo zasláno',
		'components.remindPassword.userNotExist' => 'Tento uživatel neexistuje'
		'components.registration.send' => 'Registrovat se'
	];


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
