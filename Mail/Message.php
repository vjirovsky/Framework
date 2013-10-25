<?php

namespace Schmutzka\Mail;

use Nette;


class Message extends Nette\Mail\Message
{
	/** @var int */
	public $emailId;

	/** @inject @var Models\Email */
	public $emailModel;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;


	/**
	 * @param string
	 * @param array
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
		$template->setFile($this->paramService->modulesDir . '/EmailModule/templates/@blankEmail.latte');

		$replaceArray = [];
		foreach ($values as $key => $value) {
			$key = '%' . strtoupper($key) . '%';
			$replaceArray[$key] = $value;
		}

		$body = strtr($email['body'], $replaceArray);

		$this->setSubject($email['subject']);
		$this->setHtmlBody($body);
	}

}
