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
use Nette\PhpGenerator\ClassType;


class SchmutzkaExtension extends CompilerExtension
{

	public function afterCompile(ClassType $class)
	{
		parent::afterCompile($class);

		$init = $class->methods['initialize'];
		$init->addBody('Schmutzka\DI\Extensions\FormExtension::register();');
	}

}
