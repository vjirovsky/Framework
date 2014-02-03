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


trait TMailLogger
{
	/** @inject @var Models\EmailLog */
	public $emailLogModel;

	/** @var Zenify\ParamService */
	private $paramService;


	/**
	 * @param Nette\Mail\Message
	 */
	public function send(Nette\Mail\Message $message)
	{
		$this->emailLogModel->insert($this->getData($message, TRUE));
		parent::send($message);
	}


	/**
	 * @param Nette\Mail\Message
	 * @param bool
	 * @return  []
	 */
	private function getData(Nette\Mail\Message $message, $db = FALSE)
	{
		$to = $message->getHeader('To');
		$from = $message->getHeader('From');

		if (isset($message->emailId) == FALSE) {
			return;
		}

		$data = [
			'email_id' => $message->emailId,
			'datetime' => new Nette\DateTime,
			'to_email' => key($to),
			'to_name' => array_pop($to),
			'subject' => $message->getHeader('Subject'),
			'html' => $message->getHtmlBody(),
			'body' => $message->getBody(),
		];

		if ( ! $db) {
			$data['to'] = $message->getHeader('To');
			$data['from'] =  $message->getHeader('From');
		}

		$data = array_merge($data, $this->loggerData);

		return $data;
	}

}
