<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Components\WebLoader\Filter;

use Nette;
use WebLoader;
use lessc;

class LessFilter extends Nette\Object
{
	/** @inject @var lessc */
	public $lessc;


	/**
	 * @param string
	 * @param WebLoader\Compiler
	 * @return string
	 */
	public function __invoke($code, WebLoader\Compiler $loader)
	{
		return $this->lessc->parse($code);
	}

}
