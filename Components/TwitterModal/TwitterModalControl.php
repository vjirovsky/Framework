<?php

namespace Schmutzka\Components;

use Schmutzka\Application\UI\Control;


class TwitterModalControl extends Control
{

	protected function setupLayoutTemplate()
	{
		$this->template->layoutTemplate = __DIR__ . '/templates/default.latte';
		$this->template->id = sha1(__CLASS__ . rand(0,100));
	}

}
