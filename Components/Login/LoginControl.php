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

	/** @inject @var Schmutzka\ParamService */
	public $paramService;


	public function __construct(Nette\Localization\ITranslator $translator = NULL)
	{
		$this->translator = $translator ?: new LoginControlCzechTranslator();
	}


	protected function createComponentForm()
	{
		$form = new Form;

		$form->addText('email', 'forms.login.email')
			->addRule($form::FILLED, 'forms.login.emailFilledRule')
			->addRule($form::EMAIL, 'forms.login.emailFormatRule');

		$form->addPassword('password', 'forms.login.password')
			->addRule($form::FILLED, 'forms.login.passwordFilledRule')
			->addRule($form::MIN_LENGTH, 'forms.login.passwordLengthRule', 5);

		$form->addSubmit('send', 'forms.login.send')
			->setAttribute('class', 'btn btn-success');

		return $form;
	}


	public function processForm($form)
	{
		try {
			$values = $form->values;
			$this->user->login($values['email'], $values['password']);

		} catch (Nette\Security\AuthenticationException $e) {
			$this->presenter->flashMessage($e->getMessage(), 'danger');
			return;
		}

		$this->customRedirect();
	}


	protected function customRedirect()
	{
		$this->presenter->restoreRequest($this->presenter->backlink); // @todo: fix
		$this->presenter->redirect('Homepage:default');
	}


	protected function renderAdmin()
	{
		$form = $this['form'];
		$form->id = 'loginform';
		$form['email']->setAttribute('class', 'form-control')
			->setAttribute('placeholder', 'Email');
		$form['password']->setAttribute('class', 'form-control')
			->setAttribute('placeholder', 'Password');
		$form['send']->setAttribute('class', 'btn btn-success');
	}

}
