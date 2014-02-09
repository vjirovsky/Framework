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


trait TModulePresenter
{
	use Zenify\Security\TCheckRequirements;

	/** @persistent @var int */
	public $id;

	/** @var string[] */
	public $moduleParams;

	/** @inject @var App\Users */
	public $users;

	/** @inject @var Zenify\Components\IAdminMenuControl */
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
	public function handleDelete($id)
	{
		$entity = $this->dao->find($id);
		$this->dao->delete($entity);
		$this->redirect('this', ['id' => NULL]);
	}


	/**
	 * @param  int
	 */
	public function renderEdit($id)
	{
		$this->template->item = $item = $this->dao->find($id);
	}


	/**
	 * @return  Nette\ArrayHash
	 */
	public function getModuleParameters()
	{
		return $this->paramService->getModuleParameters($this->presenter->module);
	}


	/**
	 * @return  App\*s
	 */
	public function getDao()
	{
		$name = Name::daoFromPresenter($this);
		return $this->$name;
	}

}
