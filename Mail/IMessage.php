<?php

namespace Schmutzka\Mail;

use Nette;


interface IMessage
{
	/** @return Message */
	function create();

}
