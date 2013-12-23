<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Mail;

use Nette;
use Schmutzka;
use Schmutzka\Models;


class SmtpMailer extends Nette\Mail\SmtpMailer
{
	/** @inject @var Models\Email */
	public $emailModel;

	/** @inject @var Models\EmailLog */
	public $emailLogModel;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @var int */
	private $debugMode;



	public function __construct(Schmutzka\ParamService $paramService)
	{
		parent::__construct((array) $paramService->mailer);
	}


	/**
	 * Send email
	 * @param Nette\Mail\Message
	 */
	public function send(Nette\Mail\Message $message)
	{
		// default headers prevents error
		if ( ! $message->getHeader('From')) {
			$message->setFrom('example@gmail.com'); // replaced by login email
		}

		parent::send($message);
	}


	/**
	 * Get mail data
	 * @param Nette\Mail\Message
	 * @param bool
	 * @return array
	 */
	private function getData(Nette\Mail\Message $message, $db = FALSE)
	{
		$to = $message->getHeader('To');
		$from = $message->getHeader('From');

		$array = [
			'email_id' => $this->emailId,
			'datetime' => new Nette\DateTime,
			'to_email' => key($to),
			'to_name' => array_pop($to),
			'subject' => $message->getHeader('Subject'),
			 'html' => $message->getHtmlBody(),
			 'body' => $message->getBody(),
		];

		if ( ! $db) {
			$array['to'] = $message->getHeader('To');
			$array['from'] =  $message->getHeader('From');
		}

		$array = array_merge($array, $this->loggerData);

		return $array;
	}

}
