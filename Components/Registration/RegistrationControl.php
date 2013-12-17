<?php

namespace Components;

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

	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @var string */
	private $role;


	protected function createComponentForm()
	{
		$userModel = $this->userModel;

		$form = new Form;

		$form->addText('email', 'Email')
			->addRule(Form::FILLED, 'Zadejte email')
			->addRule(Form::EMAIL, 'Opravte formáte emailu')
			->addRule(function ($input) use ($userModel) {
				return ! $userModel->fetch(['email' => $input->value]);
			}, 'Tento email je již registrován. Použijte jiný.');

		$form->addPassword('password', 'Heslo')
			->addRule(Form::FILLED, 'Zadejte heslo')
			->addRule(Form::MIN_LENGTH, 'Heslo musí mít aspoň %d znaků', 5);

		$form->addSubmit('send', 'Registrovat se');

		return $form;
	}


	public function processForm($form)
	{
		$rawValues = $values = $form->values;
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

		$loginColumn = (isset($this->paramService->loginColumn) ? $this->paramService->loginColumn : 'email');
		$this->user->login($values[$loginColumn], $rawValues['password']);

		$this->presenter->flashMessage($this->paramService->registration->onSuccessAndLogin, 'success');
		$this->redirect('this');
	}

}
