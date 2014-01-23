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

use Schmutzka\Application\UI\Control;
use Schmutzka\Application\UI\Form;
use Schmutzka\Security\UserManager;


/**
 * @method setRole(string)
 */
class RegistrationControl extends Control
{
	/** @inject @var Models\User */
	public $userModel;

	/** @inject @var Schmutzka\Security\User */
	public $user;

	/** @inject @var Schmutzka\Security\UserManager */
	public $userManager;

	/** @var string */
	protected $role;


	public function __construct(Nette\Localization\ITranslator $translator = NULL)
	{
		$this['form']->setTranslator($translator ?: new RegistrationControl\Localization\CzechTranslator);
	}


	protected function createComponentForm()
	{
		$form = new Form;
		$form->addText('email', 'components.email')
			->addRule(Form::FILLED, 'components.emailFilledRule')
			->addRule(Form::EMAIL, 'components.emailFormatRule')
			->addRule(function ($input) {
				return ! $this->userModel->fetch(['email' => $input->value]);
			}, 'components.registration.alreadyExists');

		$form->addPassword('password', 'components.password')
			->addRule(Form::FILLED, 'components.passwordRuleFilled');

		$form->addSubmit('send', 'components.registration.send');

		return $form;
	}


	public function processForm($values)
	{
		$rawValues = $values;
		unset($values['conditions']);

		if ($this->role) {
			$values['role'] = $this->role;
		}

		try {
			$this->userManager->register($values);

		} catch (\Exception $e) {
			$this->presenter->flashMessage($e->getMessage(), 'danger');
			$this->redirect('this');
		}

		$this->user->login($values['email'], $rawValues['password']);
		$this->redirect('this');
	}

}
