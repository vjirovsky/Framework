<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Application\UI;

use Nette;
use Zenify;
use Zenify\Utils\Name;
use Zenify;


abstract class Presenter extends Nette\Application\UI\Presenter
{
	use Zenify\Diagnostics\Panels\TCleanerPanel;
	use Zenify\Security\TCheckRequirements;
	use Zenify\TemplateFactory\Templating\TTemplateFactory;
	use TCreateComponent;

	/** @persistent @var string */
	public $backlink;

	/** @var string */
	public $module;

	/** @inject @var Zenify\ParamService */
	public $paramService;

	/** @inject @var Zenify\Components\ITitleControl */
	public $titleControl;

	/** @inject @var Zenify\Components\IFlashMessageControl */
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
