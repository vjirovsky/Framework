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

use Nette;
use Nette\Mail\Message;
use Zenify\Application\UI\Form;
use Zenify\Application\UI\Control;


/**
 * @method setSubjectText(bool)
 * @method getSubjectText()
 * @method setIncludeParams(bool)
 * @method getIncludeParams()
 * @method setShowEmail(bool)
 * @method getShowEmail()
 * @method setMailFrom(string)
 * @method getMailFrom()
 * @method setLogSender(bool|array)
 * @method getLogSender()
 */
class ContactControl extends Control
{
	/** @inject @var Nette\Http\Request */
	public $httpRequest;

	/** @inject @var Zenify\Security\User*/
	public $user;

	/** @inject @var Nette\Mail\IMailer */
	public $mailer;

	/** @inject @var Zenify\ParamService */
	public $paramService;

	/** @var array */
	private $logSender = [];

	/** @var string */
	private $mailFrom;

	/** @var bool */
	private $showEmail = TRUE;

	/** @var string */
	private $subjectText = 'Kontaktní formulář';

	/** @var bool */
	private $includeParams = FALSE;


	protected function createComponentForm()
	{
		$form = new Form;

		if ($this->showEmail) {
			$form->addText('email', 'Váš email:')
				->addRule(Form::FILLED, 'Zadejte Váš email')
				->addRule(Form::EMAIL, 'Email nemá správný formát');
		}

		$form->addTextarea('text', 'Zpráva:')
			->addRule(Form::FILLED, 'Napište Váš dotaz');

		$form->addAntispam();
		$form->addSubmit('send', 'Odeslat')
			->setAttribute('class', 'btn btn-success');

		return $form;
	}


	public function processForm($values, $form)
	{

		$domain = $this->httpRequest->url->host;

		$text = 'Dobrý den,\n\nze stránky ' .
			$domain .
			' Vám byla zaslána následující zpráva:\n\n' .
			$values['text'];

		if ($this->includeParams) {
			$text .= '\n\nVeškeré parametry:\n';
			foreach ($form->components as $key => $component) {
				if ($key != 'submit') {
					$text .= $component->caption . ' ' . $component->value . '\n';
				}
			}
		}

		/*
		if (!isset($values['email'])) {
			$values['email'] = 'no-reply@' . $domain;
		}
		*/

		$message = new Message;
		$message->setFrom($values['email'])
			->setSubject(rtrim($domain . ' - ' . $this->subjectText, ' - '));

		$from = '';
		if ($this->logSender) {
			if ($this->user->loggedIn) {
				$name = '';
				foreach ($this->logSender as $key) {
					if (isset($this->user->identity->{$key})) {
						$name .= $this->user->identity->{$key} . ' ';
					}
				}

				$message->setFrom($this->user->email, trim($name));
				$from = 'Od: ' . trim($name) . ', '. $this->user->email . "\n\n";

			} else {
				$key = array_shift($this->logSender);
				$email = $values[$key];
				$from = 'Od: ' . $email;

				if ($key = array_shift($this->logSender)) {
					$name = trim($values[$key]);
					$from .= ', ' . $name;
				}

				$from .= '\n\n';
				$message->setFrom($email ?: $this->mailFrom, $name);
			}
		}

		$mailTo = $this->paramService->contactControl->mailTo;

		if (is_array($mailTo)) {
			foreach ($mailTo as $value) {
				$message->addTo($value);
			}

		} else {
			$message->addTo($mailTo);
		}

		$message->setBody($from . $text);
		$this->mailer->send($message);

		$this->presenter->flashMessage('Zpráva byla úspěšně odeslána.', 'success');
		$this->presenter->redirect('this');
	}

}
