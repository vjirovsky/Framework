<?php

namespace Schmutzka\Templating;

use Nette;
use Nette\Application\UI\Presenter;


class TemplateFactory extends Nette\Object implements ITemplateFactory
{
	/** @var Nette\DI\Container */
	private $container;

	/** @var callback[] */
	private $helperLoaders = [];

	/** @var string[] */
	private $filters = [];

	/** @var string[] */
	private $macroLoaders = [];


	public function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}


	/**
	 * @param callback
	 */
	public function addHelperLoader($callback)
	{
		$this->helperLoaders[] = $callback;
	}


	/**
	 * @param string
	 */
	public function addFilter($service)
	{
		$this->filters[] = $service;
	}


	/**
	 * @param string
	 */
	public function addMacroLoader($service)
	{
		$this->macroLoaders[] = $service;
	}


	public function createTemplate(Nette\Application\UI\Control $control, $class = NULL)
	{
		$template = $class ? new $class : new Nette\Templating\FileTemplate;
		$presenter = $control->getPresenter(FALSE);
		$template->onPrepareFilters[] = $control->templatePrepareFilters;

		foreach ($this->filters as $filter) {
			$template->registerFilter($filter);
		}
		$template->registerFilter($latte = new Nette\Latte\Engine);

		$template->registerHelperLoader('Nette\Templating\Helpers::loader');
		foreach ($this->helperLoaders as $callback) {
			$template->registerHelperLoader([$callback, 'loader']);
		}

		foreach ($this->macroLoaders as $macroLoader) {
			$macroLoader::install($latte->compiler);
		}

		// default parameters
		$template->control = $template->_control = $control;
		$template->presenter = $template->_presenter = $presenter;
		if ($presenter instanceof Presenter) {
			$template->setCacheStorage($this->container->getService('nette.templateCacheStorage'));
			$template->user = $presenter->getUser();
			$template->netteHttpResponse = $this->container->getByType('Nette\Http\IResponse');
			$template->netteCacheStorage = $this->container->getByType('Nette\Caching\IStorage');
			$template->baseUri = $template->baseUrl = rtrim($this->container->getByType('Nette\Http\IRequest')->getUrl()->getBaseUrl(), '/');
			$template->basePath = preg_replace('#https?://[^/]+#A', '', $template->baseUrl);

			// flash message
			if ($presenter->hasFlashSession()) {
				$id = $control->getParameterId('flash');
				$template->flashes = $presenter->getFlashSession()->$id;
			}
		}

		if ( ! isset($template->flashes) || ! is_array($template->flashes)) {
			$template->flashes = array();
		}

		return $template;
	}

}
