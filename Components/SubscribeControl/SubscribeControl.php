<?php

namespace Components;

use Schmutzka\Application\UI\Form;
use Schmutzka\Application\UI\Control;


class SubscribeControl extends Control
{
	/** @inject @var Models\Subscription */
	public $subscriptionModel;


	protected function createComponentForm()
	{
		$form = new Form;
		$form->addText('email', 'Your mail')
			->setAttribute('placeholder', 'your@email.com')
			->addRule(Form::FILLED, 'Fill your email address')
			->addRule(Form::EMAIL, 'Correct email address format');
		$form->addSubmit('send', 'Request an Invite')
			->setAttribute('class', 'btn btn-success');

		return $form;
	}


	public function processForm($form)
	{
		$values = $form->values;
		$this->subscriptionModel->insert($values);
		$this->redirect('this', ['sent' => 1]);
	}


	protected function renderDefault()
	{
		$this->template->sent = $this->getParam('sent');
	}

}
