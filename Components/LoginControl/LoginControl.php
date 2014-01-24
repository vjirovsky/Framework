<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Components;

use Nette;
use Schmutzka;
use Schmutzka\Application\UI\Control;
use Schmutzka\Application\UI\Form;


class LoginControl extends Control
{
	use Schmutzka\Localization\TComponentSimpleTranslator;

	/** @inject @var Nette\Security\User */
	public $user;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;


	protected function createComponentForm()
	{
		$form = new Form;

		$form->addText('email', 'components.email')
			->addRule($form::FILLED, 'components.emailFilledRule')
			->addRule($form::EMAIL, 'components.emailFormatRule');

		$form->addPassword('password', 'components.password')
			->addRule($form::FILLED, 'components.passwordFilledRule');

		$form->addSubmit('send', 'components.login.send')
			->setAttribute('class', 'btn btn-success');

		return $form;
	}


	public function processForm($values)
	{
		try {
			$this->user->login($values['email'], $values['password']);

		} catch (Nette\Security\AuthenticationException $e) {
			$this->presenter->flashMessage($this->translate($e->getMessage()), 'danger');
			$this->redirect('this');
		}

		$this->customRedirect();
	}


	protected function customRedirect()
	{
		$this->presenter->restoreRequest($this->presenter->backlink); // @todo: fix
		$this->presenter->redirect('Homepage:default');
	}

}
