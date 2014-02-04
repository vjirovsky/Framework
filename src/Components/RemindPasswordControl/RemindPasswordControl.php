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
use Nette\Utils\Strings;


class RemindPasswordControl extends Control
{
	use Zenify\Forms\Rendering\TModuleRenderer;
	use Zenify\Localization\TComponentSimpleTranslator;

	/** @inject @var Models\User */
	public $userModel;

	/** @inject @var Nette\Mail\IMailer */
	public $mailer;

	/** @inject @var Zenify\Security\UserManager */
	public $userManager;

	/** @inject @var Zenify\Mail\IMessage */
	public $message;

	/** @inject @var Zenify\ParamService */
	public $paramService;



	protected function createComponentForm()
	{
		$form = new Form;

		$form->addText('email', 'components.email')
			->addRule($form::FILLED, 'components.emailFilledRule')
			->addRule($form::EMAIL, 'components.emailFormatRule');

		$form->addSubmit('send', 'components.remindPassword.send')
			->setAttribute('class', 'btn btn-success');

		return $form;
	}


	public function processForm($values, $form)
	{
		if ($this->userModel->fetch(['email' => $values['email']])) {
			$message = $this->message->create();
			$message->setFrom($this->paramService->email->from)
				->addTo($values['email']);

			$values['password'] = $password = Strings::random(10);
			$salt = UserManager::makeSalt();
			$newValues = [
				'salt' => $salt,
				'password' => UserManager::hashPassword($password, $salt)
			];
			$this->userModel->update($newValues, ['email' => $values['email']]);


			// dd($password, $newValues);

			$message->addCustomTemplate($this->getMessageUid(), $values);
			$this->mailer->send($message);

			$this->presenter->flashMessage('components.remindPassword.newPasswordSetUp', 'success');

		} else {
			$this->presenter->flashMessage('components.remindPassword.userNotExist', 'danger');
		}

		$this->redirect('this');
	}


	/**
	 * @return  string
	 */
	private function getMessageUid()
	{
		return 'remindPassword' . ($this->translator ? '_' . $this->translator->getLocale() : NULL);
	}


	protected function renderAdmin()
	{
		$this->setupModuleRenderer($this['form']);
	}

}
