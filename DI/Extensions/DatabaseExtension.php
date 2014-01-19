<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\DI\Extensions;

use Nette;
use Nette\DI\CompilerExtension;


class DatabaseExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('securityManager'))
			->setClass('Schmutzka\Security\UserManager');

		$builder->addDefinition($this->prefix('users'))
			->setClass('Models\User');
	}

}
