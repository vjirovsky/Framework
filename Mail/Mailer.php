<?php

namespace Schmutzka\Mail;

use Nette;
use Schmutzka;
use Schmutzka\Mail\MailLoggerTrait;


class Mailer extends Nette\Mail\SendmailMailer
{
	use MailLoggerTrait;

}
