<?php

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

	/** @inject @var Components\IAdminMenuControl */
	public $adminMenuControl;

	/** @var string */
	protected $unloggedRedirect = 'Homepage:default';

	/** @var Nette\Security\Permission */
	private $acl;


	public function injectDependencies(Nette\Security\IAuthorizator $acl = NULL)
	{
		$this->acl = $acl;
	}


	public function startup()
	{
		parent::startup();

		if ( ! $this->user->loggedIn) {
			$this->layout = 'layoutLogin';
		}

		if ($this->user->loggedIn == FALSE && $this->presenter->isLinkCurrent('Homepage:default') == FALSE) {
			$this->flashMessage('Pro přístup do této sekce se musíte přihlásit.', 'info');
			$this->redirect(':Admin:Homepage:default');

		} elseif ($this->acl && ! $this->user->isAllowed($this->name, $this->action)) {
			// $this->flashMessage('Na vstup do této sekce nemáte povolený vstup.', 'error');
			$this->flashMessage('Byli jste úspěšně přihlášeni.', 'success');
			$this->redirect(':Front:Homepage:logged'); // custom manage
		}

		$this->template->modules = $this->paramService->getActiveModules();

		if (property_exists($this, 'translator')) {
			$this->template->adminLangs = $this->paramService->adminLangs;
			$this->template->lang = $this->lang;
		}

		$this->template->modules = $modules = $this->paramService->getActiveModules();
		$this->template->useCkeditor = $this->useCkeditor($modules);
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
	public function getModuleParams()
	{
		return $this->paramService->getModuleParams($this->presenter->module);
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


	/**
	 * @param  array
	 * @return boolean
	 */
	private function useCkeditor($modules)
	{
		foreach ($modules as $value) {
			if (in_array($value, ['page', 'article', 'email'])) {
				return TRUE;
			}
		}

		return FALSE;
	}

}
