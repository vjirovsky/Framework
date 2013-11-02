<?php

namespace Schmutzka\Application\UI;

use Nette;
use Schmutzka;
use Schmutzka\Utils\Name;


abstract class Presenter extends Nette\Application\UI\Presenter
{
	use CreateComponentTrait;
	use Schmutzka\Diagnostics\Panels\CleanerPanelTrait;

	/** @persistent @var string */
	public $backlink;

	/** @var string */
	public $module;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @inject @var Schmutzka\Templates\TemplateService */
	public $templateService;

	/** @inject @var Components\ITitleControl */
	public $titleControl;

	/** @inject @var Components\IFlashMessageControl */
	public $flashMessageControl;
	/** @var array|callable[] */
	public $helpersCallbacks = [];



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
	 * @param string
	 */
	public function createTemplate($class = NULL)
	{
		$template = parent::createTemplate($class);
		$this->templateService->configure($template);

		foreach ($this->helpersCallbacks as $helpersCallback) {
			$template->registerHelperLoader($helpersCallback);
		}

		return $template;
	}


	public function formatLayoutTemplateFiles()
	{
		$layoutTemplateFiles = parent::formatLayoutTemplateFiles();
		$layoutTemplateFiles[] = $this->paramService->modulesDir . '/@' . ($this->layout ?: 'layout') . '.latte';
		$layoutTemplateFiles[] = $this->paramService->appDir . '/FrontModule/templates/@' . ($this->layout ?: 'layout') . '.latte';

		return $layoutTemplateFiles;
	}

}
