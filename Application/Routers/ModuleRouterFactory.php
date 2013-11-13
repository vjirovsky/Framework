<?php

namespace Schmutzka\Application\Routers;

use Models;
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


	/**
	 * @return RouteList
	 */
	protected function createModuleRouter(RouteList $router)
	{
		$modules = (array) $this->paramService->modules;
		$router[] = new Route('<module admin|' . Name::upperToDashedLower(implode($modules, '|')) . '>/<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}


	protected function addPageRouter(RouteList $frontRouter)
	{
		$frontRouter[] = new PairsRoute('<uid [A-z-]+>', 'Page:detail', $this->pageModel, $this->cache, 'uid', 'title');
		$frontRouter[] = new PairsRoute('<id [1-9]+>', 'Page:detail', $this->pageModel, $this->cache, 'id', 'title');
	}


	protected function addArticleRouter(RouteList $frontRouter)
	{
		$frontRouter[] = new PairsRoute('clanek/<id>', 'Article:detail', $this->articleModel, $this->cache, 'id', 'url');
	}


	protected function addNewsRouter(RouteList $frontRouter)
	{
		$frontRouter[] = new PairsRoute('aktualita/<id>', 'News:detail', $this->newsModel, $this->cache, 'id', 'title');
	}


}
