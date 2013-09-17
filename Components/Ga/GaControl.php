<?php

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
	 * @param string
	 */
	protected function renderDefault($code, $domain)
	{
		$this->template->code = $code;
		$this->template->domain = $domain;
	}

}
