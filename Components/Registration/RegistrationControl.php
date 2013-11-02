<?php

namespace Components;

use Nette;
use Nette\DateTime;
use Nette\Utils\Strings;
use Schmutzka;
use Schmutzka\Application\UI\Control;
use Schmutzka\Application\UI\Form;
use Schmutzka\Mail\Message;
use Schmutzka\Security\UserManager;


/**
 * @method setSendSuccessEmail(bool)
 * @method setRole(stringl)
 */
class RegistrationControl extends Control
{
	/** @inject @var Nette\Mail\IMailer */
	public $mailer;

	/** @inject @var Schmutzka\Mail\IMessage */
	public $message;

	/** @inject @var Models\User */
	public $userModel;

	/** @inject @var Schmutzka\Security\User */
	public $user;

	/** @inject @var Schmutzka\Security\UserManager */
	public $userManager;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;


	/** @var bool */
	private $sendSuccessEmail = FALSE;

	/** @var string */
	private $role;


	protected function createComponentForm()
	{
		$userModel = $this->userModel;

		$form = new Form;

		$form->addText('email', $this->paramService->form->email->label)
			->addRule(Form::FILLED, $this->paramService->form->email->ruleFilled)
			->addRule(Form::EMAIL, $this->paramService->form->email->ruleFormat)
			->addRule(function ($input) use ($userModel) {
				return ! $userModel->fetch(['email' => $input->value]);
			}, $this->paramService->form->email->alreadyExists);

		$form->addPassword('password', $this->paramService->form->password->label)
			->addRule(Form::FILLED, $this->paramService->form->password->ruleFilled)
			->addRule(Form::MIN_LENGTH, $this->paramService->form->password->length, 5);

		$form->addSubmit('send', $this->paramService->form->send->register)
			->setAttribute('class', 'btn btn-primary');

		return $form;
	}


	public function processForm($form)
	{
		$rawValues = $values = $form->values;
		unset($values['conditions']);

		if ($this->role) {
			$values['role'] = $this->role;
		}

		$this->userManager->register($values);

		if ($this->sendSuccessEmail) {
			$this->sendSuccessEmail($values);
		}

		$loginColumn = (isset($this->paramService->loginColumn) ? $this->paramService->loginColumn : 'email');
		$this->user->login($values[$loginColumn], $rawValues['password']);
		$this->presenter->flashMessage($this->paramService->registration->onSuccessAndLogin, 'success');


		$this->redirect('this');
	}


	/**
	 * @param array
	 */
	private function sendSuccessEmail($values)
	{
		if ( ! isset($this->paramService->email->from)) {
			throw new \Exception('Missing param email.from');
		}

		$message = $this->message->create();
		$message->setFrom($this->paramService->email->from);
		$message->addTo($values['email']);
		$message->addCustomTemplate('registration', $values);

		$this->mailer->send($message);
	}

}
