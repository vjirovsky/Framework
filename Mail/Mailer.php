<?php

namespace Schmutzka\Mail;

use Nette;
use Schmutzka;


class Mailer extends Nette\Mail\SendmailMailer
{
	use Schmutzka\Mail\TMailLogger;

}
