<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Components;

use Zenify;
use Zenify\Application\UI\Form;
use Zenify\Application\UI\Control;
use Zenify\Security\UserManager;


class ChangePasswordControl extends Control
{
	use Zenify\Forms\Rendering\TModuleRenderer;
	use Zenify\Localization\TComponentSimpleTranslator;

	/** @inject @var App\Users */
	public $users;


	protected function createComponentForm()
	{
		$form = new Form;

		$form->addPassword('oldPassword', 'components.changePassword.oldPassword')
			->addRule(Form::FILLED, 'components.changePassword.oldPasswordRuleFilled');
		$form->addPassword('password', 'components.changePassword.newPassword')
			->addRule(Form::FILLED, 'components.changePassword.newPasswordRuleFilled');
		$form->addSubmit('send', 'components.changePassword.send')
			->setAttribute('class', 'btn btn-success');

		return $form;
	}


	public function processForm($values, $form)
	{
		$user = $this->users->find($this->user->id);
		$oldPass = UserManager::calculateHash($values['oldPassword'], $user->salt);

		if ($oldPass != $user->password) {
			$this->presenter->flashMessage('components.changePassword.wrongPassword', 'danger');

		} else {
			$user->password = UserManager::calculateHash($values['password'], $user->salt);
			$this->users->save($user);

			$this->presenter->flashMessage('components.changePassword.passwordChanged', 'success');
		}

		$this->presenter->redirect('this');
	}


	protected function renderAdmin()
	{
		$this->setupModuleRenderer($this['form']);
	}

}
