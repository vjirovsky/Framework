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
use Schmutzka;
use Schmutzka\Utils\Name;


abstract class Presenter extends Nette\Application\UI\Presenter
{
	use Schmutzka\Diagnostics\Panels\TCleanerPanel;
	use Schmutzka\Security\TCheckRequirements;
	use Schmutzka\Templating\TTemplateFactory;
	use TCreateComponent;

	/** @persistent @var string */
	public $backlink;

	/** @var string */
	public $module;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @inject @var Schmutzka\Components\ITitleControl */
	public $titleControl;

	/** @inject @var Schmutzka\Components\IFlashMessageControl */
	public $flashMessageControl;


	public function startup()
	{
		parent::startup();
		$this->module = Name::module($this->presenter);
		$this->checkTitleAnnotation();
	}


	public function handleLogout()
	{
		$this->user->logout();

		if ($this->module) {
			$this->redirect(':Front:Homepage:default');

		} else {
			$this->redirect('Homepage:default');
		}
	}


	/**
	 * @param  array
	 * @return  boolean
	 */
	public function isLinkCurrentOneOf($links = [])
	{
		foreach ($links as $link) {
			if ($this->isLinkCurrent($link)) {
				return TRUE;
			}
		}

		return FALSE;
	}


	private function checkTitleAnnotation()
	{
		$reflection = $this->getReflection();

		if ($reflection->hasMethod($method = $this->formatActionMethod($this->view))) {
			$reflectionMethod = $reflection->getMethod($method);
			if ($reflectionMethod->hasAnnotation('title')) {
				$this['titleControl']->addTitle($reflectionMethod->getAnnotation('title'));
			}

		} elseif ($reflection->hasMethod($method = $this->formatRenderMethod($this->view))) {
			$reflectionMethod = $reflection->getMethod($method);
			if ($reflectionMethod->hasAnnotation('title')) {
				$this['titleControl']->addTitle($reflectionMethod->getAnnotation('title'));
			}
		}
	}

}
