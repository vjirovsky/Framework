<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Application\UI;

use Nette;
use Nette\Utils\Strings;
use Schmutzka;


abstract class Control extends Nette\Application\UI\Control
{
	use Schmutzka\Templating\TTemplateFactory;
	use TCreateComponent;

	/** @var Nette\Localization\ITranslator */
	protected $translator;


	public function __construct(Nette\Localization\ITranslator $translator = NULL)
	{
		parent::__construct();
		$this->translator = $translator;
	}


	/**
	 * Rendering view
	 * @param  string
	 * @param  array
	 */
	public function __call($name, $args)
	{
		if (Strings::startsWith($name, 'render')) {
			if ($name == 'render') {
				$view = 'default';

			} else {
				$view = lcfirst(substr($name, 6));
			}

			// setup template file
			$class = $this->getReflection();
			$dir = dirname($class->getFileName());
			$this->template->setFile($dir . '/templates/' . $view . '.latte');

			// calls $this->render<View>()
			$renderMethod = 'render' . ucfirst($view);
			if (method_exists($this, $renderMethod)) {
				call_user_func_array(array($this, $renderMethod), $args);
			}

			// translator
			if (property_exists($this->presenter, 'translator')) {
				$this->template->setTranslator($this->presenter->translator);
			}

			$this->template->render();
		}
	}

}
