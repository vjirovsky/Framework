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
use Zenify\Application\UI\Control;
use Zenify\Application\UI\Form;
use Zenify\Security\UserManager;


/**
 * @method setRole(string)
 */
class RegistrationControl extends Control
{
	use Zenify\Localization\TComponentSimpleTranslator;

	/** @inject @var Models\User */
	public $userModel;

	/** @inject @var Zenify\Security\User */
	public $user;

	/** @inject @var Zenify\Security\UserManager */
	public $userManager;

	/** @var string */
	private $role;


	protected function createComponentForm()
	{
		$form = new Form;
		$form->addText('email', 'components.email')
			->addRule(Form::FILLED, 'components.emailFilledRule')
			->addRule(Form::EMAIL, 'components.emailFormatRule')
			->addRule(function ($input) {
				return ! $this->userModel->count(['email' => $input->value]);
			}, 'components.registration.alreadyExists');

		$form->addPassword('password', 'components.password')
			->addRule(Form::FILLED, 'components.passwordRuleFilled');

		$form->addSubmit('send', 'components.registration.send');

		return $form;
	}


	public function processForm($values)
	{
		if (method_exists($this, 'preProcessValues')) {
			$values = $this->preProcessValues($values);
		}

		if ($this->role) {
			$values['role'] = $this->role;
		}

		try {
			$this->userManager->add($values);

		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'danger');
			$this->redirect('this');
		}

		$this->user->login($values['email'], $values['password']);
		$this->redirect('this');
	}

}
