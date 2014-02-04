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


class Message extends Nette\Mail\Message
{
	/** @var int */
	public $emailId;

	/** @inject @var Models\Email */
	public $emailModel;

	/** @inject @var Zenify\ParamService */
	public $paramService;


	/**
	 * @param string
	 * @param array
	 * @return  self
	 */
	public function addCustomTemplate($uid, $values = [])
	{
		$email = $this->emailModel->fetchByUid($uid);
		if ( ! $email) {
			throw new \Exception("Record with uid $uid doesn't exist.");
		}

		$email = $email->toArray();

		$this->emailId = $email['id'];

		$template = new Nette\Templating\FileTemplate();
		$template->registerFilter(new Nette\Latte\Engine());
		$template->setFile($this->paramService->appDir . '/EmailModule/templates/@blankEmail.latte');

		$replaceArray = [];
		foreach ($values as $key => $value) {
			$key = '%' . strtoupper($key) . '%';
			$replaceArray[$key] = $value;
		}

		$body = strtr($email['body'], $replaceArray);

		$this->setSubject($email['subject']);
		$this->setHtmlBody($body);

		return $this;
	}

}
