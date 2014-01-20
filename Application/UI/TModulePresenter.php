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

use Schmutzka;


trait TModulePresenter
{
	/** @persistent @var int */
	public $id;

	/** @var array */
	public $moduleParams;

	/** @inject @var Models\User */
	public $userModel;

	/** @inject @var Schmutzka\Components\IAdminMenuControl */
	public $adminMenuControl;

	/** @var Nette\Security\Permission */
	private $acl;


	public function injectAcl(Nette\Security\IAuthorizator $acl = NULL)
	{
		$this->acl = $acl;
	}


	public function startup()
	{
		parent::startup();

		if ( ! $this->user->loggedIn) {
		 	if ($this->presenter->isLinkCurrent('Homepage:default') == FALSE) {
				$this->flashMessage('Pro přístup do této sekce se musíte přihlásit.', 'info');
				$this->redirect(':Admin:Homepage:default');
			}

			$this->layout = 'layoutLogin';

		} elseif ($this->acl && ! $this->user->isAllowed($this->name, $this->action)) {
			$this->flashMessage('Na vstup do této sekce nemáte povolený vstup.', 'error');
			$this->redirect(':Front:Homepage:default');
		}

		if (property_exists($this, 'translator')) {
			$this->template->adminLangs = $this->paramService->adminLangs;
			$this->template->lang = $this->lang;
		}

		$this->template->module = $this->module;
		$this->template->modules = $modules = $this->paramService->getModules();
	}


	public function renderAdd()
	{
		if ($this->id) {
			$this->id = NULL;
			$this->redirect('this');
		}
	}


	public function renderDefault()
	{
		if ($this->id) {
			$this->id = NULL;
			$this->redirect('this');
		}
	}


	/**
	 * @param  int
	 */
	public function handleDelete($id)
	{
		$this->deleteHelper($this->model, $id);
	}


	/**
	 * @param int
	 */
	public function renderEdit($id)
	{
		$this->template->item = $this->model->fetch($id);
	}


	/**
	 * Sort helper
	 * @param  array
	 * @param string
	 */
	public function handleSort($data, $rankKey = 'rank')
	{
		$data = explode(',', $data);
		$i = 1;
		foreach ($data as $item) {
			$this->model->update(array($rankKey => $i), $item);
			$i++;
		}
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
