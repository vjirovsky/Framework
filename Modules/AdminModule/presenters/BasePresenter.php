<?php

namespace AdminModule;

use FrontModule;


abstract class BasePresenter extends FrontModule\BasePresenter
{
	/** @var array */
	public $moduleParams;

	/** @inject @var Schmutzka\Models\User */
	public $userModel;

	/** @var Schmutzka\Models\Page */
	// public $pageModel;

	/** @var Schmutzka\Models\Gallery */
	// public $galleryModel;

	/** @var string */
	protected $unloggedRedirect = 'Homepage:default';


	/*
	public function injectModels(Schmutzka\Models\Page $pageModel = NULL, Schmutzka\Models\Gallery $galleryModel = NULL)
	{
		$this->pageModel = $pageModel;
		$this->galleryModel = $galleryModel;
	}
	*/


	public function startup()
	{
		parent::startup();

		if ( ! $this->user->loggedIn) {
			$this->layout = 'layoutLogin';

			if ($this->presenter->isLinkCurrent($this->unloggedRedirect) == FALSE) {
				$this->flashMessage('Pro přístup do této sekce se musíte přihlásit.', 'info');
				$this->redirect(':Admin:Homepage:default');
			}

		} elseif ($this->context->hasService('Nette\Security\IAuthorizator') && ! $this->user->isAllowed($this->name, $this->action)) {
			$this->flashMessage('Na vstup do této sekce nemáte povolený vstup.', 'error');
			$this->redirect(':Front:Homepage:default');
		}

		$this->template->modules = $this->paramService->getActiveModules();
	}

}
