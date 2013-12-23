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


class Mailer extends Nette\Mail\SendmailMailer
{
	use Schmutzka\Mail\TMailLogger;

}
