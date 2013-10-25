<?php

namespace Schmutzka\Application\Routers;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Schmutzka;
use Schmutzka\Utils\Name;


class ModuleRouterFactory
{
	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @inject @var Nette\Caching\Cache */
	public $cache;

	/** @var Schmutzka\Models\Page */
	private $pageModel;

	/** @var Schmutzka\Models\Article */
	private $articleModel;

	/** @var Schmutzka\Models\News */
	private $newsModel;


	public function injectModels(Schmutzka\Models\Page $pageModel = NULL, Schmutzka\Models\Article $articleModel = NULL, Schmutzka\Models\News $newsModel = NULL)
	{
		$this->pageModel = $pageModel;
		$this->articleModel = $articleModel;
		$this->newsModel = $newsModel;
	}


	/**
	 * @return RouteList
	 */
	protected function createModuleRouter(RouteList $router)
	{
		$modules = (array) $this->paramService->modules;
		$router[] = new Route('<module admin|' . Name::upperToDashedLower(implode($modules, '|')) . '>/<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}


	protected function addPageRouter(RouteList &$frontRouter)
	{
		$frontRouter[] = new PairsRoute('<uid [A-z-]+>', 'Page:detail', NULL, $this->pageModel, $this->cache, $columns = ['uid', 'title']);
		$frontRouter[] = new PairsRoute('<id [1-9]+>', 'Page:detail', NULL, $this->pageModel, $this->cache, $columns = ['id', 'title']);
	}


	protected function addArticleRouter(RouteList &$frontRouter)
	{
		$frontRouter[] = new PairsRoute('clanek/<id>', 'Article:detail', NULL, $this->articleModel, $this->cache, $columns = ['id', 'url']);
	}


	protected function addNewsRouter(RouteList &$frontRouter)
	{
		$frontRouter[] = new PairsRoute('aktualita/<id>', 'News:detail', NULL, $this->newsModel, $this->cache, $columns = ['id', 'title']);
	}


}
