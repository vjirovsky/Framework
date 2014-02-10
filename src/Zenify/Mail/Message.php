<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Mail;

use Nette;
use Zenify;


class Message extends Nette\Mail\Message
{
	use Zenify\TemplateFactory\Templating\TTemplateFactory;

	/** @inject @var Zenify\ParamService */
	public $paramService;

	/** @var App\Emails */
	private $emails;


	public function __constructor(App\Emails $emails = NULL)
	{
		$this->emails = $emails;
	}


	/**
	 * @param string
	 * @param []
	 * @return  self
	 */
	public function addCustomTemplate($uid, $values = [])
	{
		$email = $this->emails->findOneBy(['uid' => $uid]);
		if ( ! $email) {
			throw new \Exception("Record with uid '$uid' doesn't exist.");
		}

		$this->setSubject($email->subject);

		$template = $this->createTemplate();
		$template->setFile($this->paramService->appDir . '/EmailModule/templates/@blankEmail.latte');

		$replace = [];
		foreach ($values as $key => $value) {
			$key = '%' . strtoupper($key) . '%';
			$replace[$key] = $value;
		}

		$body = strtr($email->body, $replace);
		$this->setHtmlBody($body);

		return $this;
	}

}
