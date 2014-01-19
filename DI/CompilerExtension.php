<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\DI;

use Nette;


class CompilerExtension extends Nette\DI\CompilerExtension
{

	/**
	 * @param  string
	 */
	public function parseFromFile($file)
	{
		$builder = $this->getContainerBuilder();
		$this->compiler->parseServices($builder, $this->loadFromFile($file));
	}

}
