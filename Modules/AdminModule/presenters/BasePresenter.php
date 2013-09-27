<?php

namespace AdminModule;

use FrontModule;
use Nette;
use Schmutzka\Application\UI\Presenter; // fix hack



abstract class BasePresenter extends /*FrontModule\BasePresenter*/Presenter
{
	/** @var array */
	public $moduleParams;

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
			$this->layout = 'layoutLogin';

			if ($this->presenter->isLinkCurrent('Homepage:default') == FALSE) {
				$this->flashMessage('Pro přístup do této sekce se musíte přihlásit.', 'info');
				$this->redirect(':Admin:Homepage:default');
			}

		} elseif ($this->acl && ! $this->user->isAllowed($this->name, $this->action)) {
			// $this->flashMessage('Na vstup do této sekce nemáte povolený vstup.', 'error');
			$this->flashMessage('Byli jste úspěšně přihlášeni.', 'success');
			$this->redirect(':Front:Homepage:logged'); // custom manage
		}

		$this->template->modules = $this->paramService->getActiveModules();

		if ($this->translator) {
			$this->template->adminLangs = $this->paramService->adminLangs;
			$this->template->lang = $this->lang;
		}
	}

}
