<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Components\ChangePasswordControl\Localization;

use Schmutzka\Forms\Localization\SimpleTranslator;


class CzechTranslator extends SimpleTranslator
{
	/** @var string[] */
	protected $messages = [
		'components.changePassword.oldPassword' => 'Staré heslo',
		'components.changePassword.oldPasswordFilledRule' => 'Zadejte staré heslo',
		'components.changePassword.newPassword' => 'Nové heslo',
		'components.changePassword.newPasswordFilledRule' => 'Zadejte nové heslo',
		'components.changePassword.send' => 'Změnit heslo'
	];

}
