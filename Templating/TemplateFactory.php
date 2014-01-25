<?php

namespace Schmutzka\Templating;

use Nette;
use Nette\Application\UI\Presenter;
use Nette\Templating\FileTemplate;


/**
 * @method addFilter(object)
 * @method addHelperLoader(object)
 * @method addMacroSet(string)
 */
class TemplateFactory extends Nette\Object implements ITemplateFactory
{
	/** @var Nette\DI\Container */
	private $container;

	/** @var object[] */
	private $filters = [];

	/** @var callback[] */
	private $helpers = [];

	/** @var object[] */
	private $helperLoaders = [];

	/** @var string[] */
	private $macroSets = [];


	public function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}


	public function addHelper($name, $callback)
	{
		$this->helpers[$name] = $callback;
	}


	public function createTemplate(Nette\Application\UI\Control $control, $class = NULL)
	{
		$template = $class ? new $class : new Nette\Templating\FileTemplate;
		$presenter = $control->getPresenter(FALSE);

		$template->onPrepareFilters[] = $this->templatePrepareFilters;

		$template->registerHelperLoader('Nette\Templating\Helpers::loader');
		foreach ($this->helperLoaders as $helperLoader) {
			$template->registerHelperLoader([$helperLoader, 'loader']);
		}
		foreach ($this->helpers as $name => $callback) {
			$template->registerHelper($name, $callback);
		}

		foreach ($this->macroSets as $macroSet) {
			if (strpos($macroSet, '::') === FALSE && class_exists($macroSet)) {
				$macroSet .= '::install';
			}

			call_user_func($macroSet, $this->latte->compiler);
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


	public function templatePrepareFilters(FileTemplate $template)
	{
		foreach ($this->filters as $filter) {
			$template->registerFilter($filter);
		}

		$template->registerFilter($this->latte);
	}


	/**
	 * @return Nette\Latte\Engine
	 */
	public function getLatte()
	{
		if (! $this->container->hasService('nette.latte')) {
			$this->container->createService('nette.latte');
		}

		return $this->container->getService('nette.latte');
	}

}
