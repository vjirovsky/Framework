<?php

namespace Schmutzka\Mail;

use Nette;
use Schmutzka;


trait MailLoggerTrait
{
	/** @inject @var Schmutzka\Models\EmailLog */
	public $emailLogModel;

	/** @var Schmutzka\ParamService */
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
