<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Components;

use Nette;
use Nette\Utils\Strings;
use Zenify;
use Zenify\Application\UI\Control;


class GaControl extends Control
{
	/** @inject @var Zenify\ParamService */
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
