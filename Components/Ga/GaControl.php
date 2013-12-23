<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Components;

use Nette;
use Nette\Utils\Strings;
use Schmutzka;
use Schmutzka\Application\UI\Control;


class GaControl extends Control
{
	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @inject @var Nette\Http\Request */
	public $httpRequest;


	/**
	 * @param string
	 */
	protected function renderDefault($code)
	{
		$this->template->code = $code;
	}

}
