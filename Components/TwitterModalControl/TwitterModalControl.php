<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

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
