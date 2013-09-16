<?php

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
