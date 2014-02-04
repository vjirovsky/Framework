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


class Mailer extends Nette\Mail\SendmailMailer
{
	use Zenify\Mail\TMailLogger;

}
