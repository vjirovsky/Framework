<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Components;

use Nette;
use Schmutzka\Application\UI\Form;
use Schmutzka\Application\UI\Control;


class LoginControl extends Control
{
	/** @inject @var Schmutzka\Security\User */
	public $user;


	protected function createComponentForm()
	{
		$form = new Form;

		$form->addText('email', 'Email')
			->addRule(Form::FILLED, 'Zadejte email')
			->addRule(Form::EMAIL, 'Opravte formát emailu');
		$form->addPassword('password', 'Heslo')
			->addRule(Form::FILLED, 'Zadejte heslo');

		$form->addSubmit('send', 'Přihlásit se')
			->setAttribute('class', 'btn btn-primary');

		return $form;
	}


	public function processForm($form)
	{
		try {
 			$values = $form->values;
			$this->user->login($values['email'], $values['password']);

			$this->presenter->restoreRequest($this->presenter->backlink);
			$this->presenter->redirect('Homepage:default');

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}

}
