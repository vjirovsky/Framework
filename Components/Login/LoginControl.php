<?php

namespace Components;

use Nette;
use Schmutzka\Application\UI\Form;
use Schmutzka\Application\UI\Control;


class LoginControl extends Control
{
	/** @persistent @var string */
	public $backlink;

	/** @inject @var Schmutzka\Security\User */
	public $user;

	/** @inject @var Nette\Http\Session */
	public $session;

	/** @var string */
	public $loginColumn = 'email';


	public function attached($presenter)
	{
		parent::attached($presenter);
		$this->backlink = $presenter->backlink;
	}


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

			$this->user->setExpiration('+ 14 days', FALSE);
			$this->user->login($values[$this->loginColumn], $values['password'], $this->loginColumn);

			$this->presenter->restoreRequest($this->backlink);
			$this->presenter->redirect('Homepage:default');

		} catch (Nette\Security\AuthenticationException $e) {
			$this->presenter->flashMessage($e->getMessage(), 'danger');
		}
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
