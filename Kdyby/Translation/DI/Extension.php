<?php

namespace Schmutzka\Kdyby\Translation\DI;

use Nette;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;


class Extension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('helperLoader'))
			->setClass('Kdyby\Translation\TemplateHelpers')
			->addTag('template.helperLoader');
	}

}
