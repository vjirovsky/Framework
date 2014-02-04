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

use Zenify\Application\UI\Form;
use Zenify\Application\UI\Control;


class SubscribeControl extends Control
{
	/** @inject @var Models\Subscription */
	public $subscriptionModel;


	protected function createComponentForm()
	{
		$form = new Form;
		$form->addText('email', 'Email')
			->setAttribute('placeholder', 'your@email.com')
			->addRule(Form::FILLED, 'Zadejte email')
			->addRule(Form::EMAIL, 'Opravte formát emailu');
		$form->addSubmit('send', 'Poslat žádost')
			->setAttribute('class', 'btn btn-success');

		return $form;
	}


	public function processForm($values)
	{
		$this->subscriptionModel->insert($values);
		$this->presenter->flashMessage('Děkujeme za zájem.', 'success');
		$this->redirect('this', ['sent' => 1]);
	}


	protected function renderDefault()
	{
		$this->template->sent = $this->getParameter('sent');
	}

}
