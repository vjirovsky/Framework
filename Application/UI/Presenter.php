<?php

namespace Schmutzka\Application\UI;

use Nette;
use Nette\Utils\Strings;
use Schmutzka;
use Schmutzka\Http\Browser;
use Schmutzka\Utils\Filer;
use Schmutzka\Utils\Name;
use WebLoader;


abstract class Presenter extends Nette\Application\UI\Presenter
{
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


	/********************** templates **********************/


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


	/********************** components **********************/


	/**
	 * Handles requests to create component
	 * @param string
	 * @return Nette\ComponentModel\IComponent
	 */
	protected function createComponent($name)
	{
		$component = parent::createComponent($name);

		if ($component == NULL) {
			if (Strings::endsWith($name, 'CssControl') || Strings::endsWith($name, 'cssControl')) {
				$part = ucfirst(substr($name, 0, -10)) ?: 'Default';
				$compiler = $this->context->{'webloader.css' . $part . 'Compiler'};
				$component = new WebLoader\Nette\CssLoader($compiler, $this->template->basePath . '/webtemp/');

			} elseif (Strings::endsWith($name, 'JsControl') || Strings::endsWith($name, 'jsControl')) {
				$part = ucfirst(substr($name, 0, -9)) ?: 'Default';
				$compiler = $this->context->{'webloader.js' . $part . 'Compiler'};
				$component = new WebLoader\Nette\JavaScriptLoader($compiler, $this->template->basePath . '/webtemp/');

			} else {
				$component = call_user_func(array($this->context, 'createService' .  ucfirst($name)));
			}
		}

		return $component;
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
		if (!$id) {
			return FALSE;
		}

		if ($item = $model->item($id)) {
			$this->template->item = $item;
			return $item;

		} else {
			$this->flashMessage('Tento záznam neexistuje.', 'error');
			$this->redirect($redirect, array(
				'id' => NULL
			));
		}
	}


	/**
	 * Helper method for clear panel (@intentionally here - presenter logic)
	 * @param  string
	 */
	public function handleRunCleaner($type)
	{
		if ($this->paramService->debugMode) {
			if ($type == 'cache') {
				$this->cache->clean(array(
					Nette\Caching\Cache::ALL => TRUE
				));

			} elseif ($type == 'webtemp') {
				Filer::emptyFolder($this->paramService->wwwDir . '/webtemp/');

			} elseif ($type == 'session') {
				$this->session->destroy();
            	$this->session->start();
			}
		}

		$this->redirect('this');
	}

}
