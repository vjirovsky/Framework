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
use Schmutzka\Application\UI\Form;
use Schmutzka\Application\UI\Control;
use Schmutzka\Security\UserManager;


class ChangePasswordControl extends Control
{
	use Schmutzka\Forms\Rendering\TModuleRenderer;
	use Schmutzka\Localization\TComponentSimpleTranslator;

	/** @inject @var Models\User */
	public $userModel;

	/** @inject @var Schmutzka\Security\User */
	public $user;


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
		$userData = $this->userModel->fetch($this->user->id);
		$oldPass = UserManager::calculateHash($values['oldPassword'], $userData['salt']);

		if ($oldPass != $userData['password']) {
			$this->presenter->flashMessage('components.changePassword.wrongPassword', 'danger');

		} else {
			$data['password'] = UserManager::calculateHash($values['password'], $userData['salt']);
			$this->userModel->update($data, $this->user->id);
			$this->presenter->flashMessage('components.changePassword.passwordChanged', 'success');
		}

		$this->presenter->redirect('this');
	}


	protected function renderAdmin()
	{
		$this->setupModuleRenderer($this['form']);
	}

}
