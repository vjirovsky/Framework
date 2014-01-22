<?php

namespace Schmutzka\Templating;

use Nette;


interface ITemplateFactory
{

	/**
	 * @return Nette\Templating\ITemplate
	 */
	function createTemplate(Nette\Application\UI\Control $control, $class = NULL);

}
