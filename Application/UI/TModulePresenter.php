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


trait TModulePresenter
{
	use Schmutzka\Security\TCheckRequirements;

	/** @persistent @var int */
	public $id;

	/** @persistent @var string */
	public $locale = 'en';

	/** @var array */
	public $moduleParams;

	/** @inject @var Models\User */
	public $userModel;

	/** @inject @var Schmutzka\Components\IAdminMenuControl */
	public $adminMenuControl;


	public function startup()
	{
		parent::startup();
		$this->template->module = $this->module;
		$this->template->modules = $this->paramService->getModules();
		$this->template->useCkeditor = $this->paramService->isCkeditorUsed();
	}


	public function checkRequirements($element)
	{
		parent::checkRequirements($element);
		if ($element instanceof Nette\Application\UI\PresenterComponentReflection) {
			$annotations = $element->getAnnotations();
			$annotations += ['secured' => TRUE, 'role' => ['admin']];
			$this->processAnnotations($annotations);
		}
	}


	/**
	 * @param  int
	 */
	public function renderEdit($id)
	{
		$this->template->item = $item = $this->getModel()->fetch($id);
	}


	/**
	 * @return  Nette\ArrayHash
	 */
	public function getModuleParameters()
	{
		return $this->paramService->getModuleParameters($this->presenter->module);
	}


	/**
	 * @return  *\Models\*
	 */
	public function getModel()
	{
		$className = $this->getReflection()->getName();
		$classNameParts = explode('\\', $className);

		$name = lcfirst(substr(array_pop($classNameParts), 0, -9));
		if ($name == 'homepage') {
			$name = lcfirst(substr(array_shift($classNameParts), 0, -6));
		}

		$modelName = $name . 'Model';

		if ( ! property_exists($this, $modelName)) {
			$modelName = lcfirst($this->module) . ucfirst($modelName);
		}

		if ( ! property_exists($this, $modelName)) {
			$modelName = lcfirst($this->module) . 'Model';
		}

		return $this->{$modelName};
	}

}
