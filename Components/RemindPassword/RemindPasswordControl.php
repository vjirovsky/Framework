<?php

namespace Components;

use Schmutzka\Application\UI\Control;
use Schmutzka\Application\UI\Form;
use Nette\Utils\Strings;


class RemindPasswordControl extends Control
{
	/** @inject @var Schmutzka\Models\User */
	public $userModel;

	/** @inject @var Nette\Mail\IMailer */
	public $mailer;

	/** @inject @var Schmutzka\Security\UserManager */
	public $userManager;

	/** @inject @var Schmutzka\Mail\IMessage */
	public $message;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;


	protected function createComponentForm()
	{
		$form = new Form;
		$form->addText('email', 'Váš email:')
			->addRule(Form::FILLED, 'Zadejte email')
			->addRule(Form::EMAIL, 'Opravte formát emailu');
		$form->addSubmit('send', 'Zaslat nové heslo')
			->setAttribute('class', 'btn btn-primary');

		return $form;
	}


	public function processForm($form)
	{
		$values = $form->values;

		if ($record = $this->userModel->fetch(['email' => $values['email']])) {
			$message = $his->message->create();
			$message//->setFrom($this->from)
				->addTo($values['email']);

			$values['new_password'] = Strings::random(10);
			$this->userManager->updatePasswordForUser([
				'email' => $values['email']
			], $password);

			$message->addCustomTemplate('remind_password', $values);
			
			$this->mailer->send($message);

			$this->presenter->flashMessage('Nové heslo bylo nastaveno. Zkontrolujte Vaši emailovou schránku.', 'success');

		} else {
			$this->presenter->flashMessage('Tento email u nás neexistuje.', 'error');
		}

		$this->presenter->redirect('this');
	}


	protected function renderAdmin()
	{
		$form = $this['form'];

		$form->id = 'recoverform';
		$form['email']->setAttribute('class', 'form-control')
			->setAttribute('placeholder', 'Zadejte Váš email');
		$form['send']->setAttribute('class', 'btn btn-success');
	}

}
