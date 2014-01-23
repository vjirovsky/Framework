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
use Schmutzka\Application\UI\Control;
use Schmutzka\Application\UI\Form;


class LoginControl extends Control
{
	/** @inject @var Nette\Security\User */
	public $user;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;


	public function __construct(Nette\Localization\ITranslator $translator = NULL)
	{
		$this->translator = $translator;
	}


	public function attached($presenter)
	{
		parent::attached($presenter);
		$this['form']->setTranslator($this->translator && $presenter->module == 'front'
			? $this->translator
			: new LoginControl\Localization\CzechTranslator);
	}


	protected function createComponentForm()
	{
		$form = new Form;

		$form->addText('email', 'components.email')
			->addRule($form::FILLED, 'components.emailFilledRule')
			->addRule($form::EMAIL, 'components.emailFormatRule');

		$form->addPassword('password', 'components.password')
			->addRule($form::FILLED, 'components.passwordFilledRule');

		$form->addSubmit('send', 'components.login.send')
			->setAttribute('class', 'btn btn-success');

		return $form;
	}


	public function processForm($values)
	{
		try {
			$this->user->login($values['email'], $values['password']);

		} catch (Nette\Security\AuthenticationException $e) {
			dd($this->translate($e->getMessage()));

			$this->presenter->flashMessage($this->translate($e->getMessage()), 'danger');
			$this->redirect('this');
		}

		$this->customRedirect();
	}


	protected function customRedirect()
	{
		$this->presenter->restoreRequest($this->presenter->backlink); // @todo: fix
		$this->presenter->redirect('Homepage:default');
	}

}
