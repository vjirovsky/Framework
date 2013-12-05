<?php

namespace Schmutzka\Application\UI;

use Nette;
use Schmutzka;
use Schmutzka\Utils\Name;


abstract class Presenter extends Nette\Application\UI\Presenter
{
	use Schmutzka\Diagnostics\Panels\TCleanerPanel;
	use Schmutzka\Templating\TTemplateSetup;
	use TCreateComponent;

	/** @persistent @var string */
	public $backlink;

	/** @var string */
	public $module;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @inject @var Components\ITitleControl */
	public $titleControl;

	/** @inject @var Components\IFlashMessageControl */
	public $flashMessageControl;


	public function startup()
	{
		parent::startup();

		$this->module = Name::mpv($this->presenter, 'module');

		if ($this->user->loggedIn && $this->user->id && $this->paramService->logUserActivity) {
			$this->user->logLastActive();
		}
	}


	public function handleLogout()
	{
		$this->user->logout();
		if ($this->paramService->flashes->onLogout) {
			$this->flashMessage($this->paramService->flashes->onLogout, 'success timeout');
		}

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
	protected function isLinkCurrentOneOf($links = [])
	{
		foreach ($links as $link) {
			if ($this->isLinkCurrent($link)) {
				return TRUE;
			}
		}

		return FALSE;
	}

}
