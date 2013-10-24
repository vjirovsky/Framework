<?php

namespace Schmutzka\Application\UI;

use Nette;
use Schmutzka;
use Schmutzka\Http\Browser;
use Schmutzka\Utils\Name;


abstract class Presenter extends Nette\Application\UI\Presenter
{
	use CreateComponentTrait;
	use Schmutzka\Diagnostics\Panels\CleanerPanelTrait;

	/** @persistent @var string */
	public $lang;

	/** @persistent @var string */
	public $backlink;

	/** @var string */
	public $module;

	/** @inject @var Nette\Caching\Cache */
	public $cache;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @inject @var Schmutzka\Templates\TemplateService */
	public $templateService;

	/** @inject @var Components\ITitleControl */
	public $titleControl;

	/** @var array|callable[] */
	public $helpersCallbacks = array();

	/** @var Nette\localization\ITranslator */
	protected $translator;


	public function injectTranslator(Nette\Localization\ITranslator $translator = NULL)
	{
		$this->translator = $translator;
	}


	public function startup()
	{
		parent::startup();

		$this->module = Name::mpv($this->presenter, 'module');

		if ($this->user->loggedIn && $this->paramService->logUserActivity) {
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
		$this->templateService->configure($template, $this->lang);

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


	/********************** module helpers **********************/


	/**
	 * @param Schmutzka\Models\Base
	 * @param int
	 * @param string
	 */
	protected function deleteHelper($model, $id, $redirect = 'default')
	{
		if (!$id) {
			return FALSE;
		}

		if ($model->delete($id)) {
			$this->flashMessage($this->paramService->flashes->onDeleteSuccess, 'success');

		} else {
			$this->flashMessage($this->paramService->flashes->onDeleteError, 'error');
		}

		if ($redirect) {
			$this->redirect($redirect, array(
				'id' => NULL
			));
		}
	}


	/**
	 * @param Schmutzka\Models\Base
	 * @param int
	 * @param string
	 */
	protected function loadItemHelper($model, $id, $redirect = 'default')
	{
		if ( ! $id) {
			return FALSE;
		}

		if ($item = $model->fetch($id)) {
			$this->template->item = $item;
			return $item;

		} else {
			$this->flashMessage('Tento záznam neexistuje.', 'error');
			$this->redirect($redirect, ['id' => NULL]);
		}
	}

}
