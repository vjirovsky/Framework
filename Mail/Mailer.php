<?php

namespace Schmutzka\Mail;

use Nette;
use Schmutzka;


class Mailer extends Nette\Mail\SendmailMailer
{
	/** @inject @var Schmutzka\Models\Email */
	public $emailModel;

	/** @inject @var Schmutzka\Models\EmailLog */
	public $emailLogModel;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @var int */
	private $emailId;


	/**
	 * @param Nette\Mail\Message
	 */
	public function send(Nette\Mail\Message $message)
	{
		// default headers prevents error
		if ( ! $message->getHeader('From')) {
			$message->setFrom('example@gmail.com'); // replaced by login email
		}

		$this->emailLogModel->insert($this->getData($message, TRUE));


		parent::send($message);
	}


	/**
	 * Use custom template from database
	 * @param string
	 * @param array
	 * @param bool
	 * @return string|array
	 */
	public function getCustomTemplate($uid, $values = [], $includeSubject = FALSE)
	{
		$email = $this->emailModel->fetchByUid($uid);
		if ( ! $email) {
			throw new \Exception("Record with uid $uid doesn't exist.");
		}

		$this->emailId = $email['id'];

		$template = new Nette\Templating\FileTemplate();
		$template->registerFilter(new Nette\Latte\Engine());
		$template->setFile($this->paramService->modulesDir . '/EmailModule/templates/@blankEmail.latte');

		$replaceArray = [];
		foreach ($values as $key => $value) {
			$key = '%' . strtoupper($key) . '%';
			$replaceArray[$key] = $value;
		}

		$body = strtr($email['body'], $replaceArray);
		if ( ! $includeSubject) {
			return $body;
		}

		$subject = strtr($email['subject'], $replaceArray);

		return [
			'body' => $body,
			'subject' => $subject
		];
	}


	/********************** helpers **********************/


	/**
	 * Get mail data
	 * @param Nette\Mail\Message
	 * @param bool
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

		return $array;
	}

}
