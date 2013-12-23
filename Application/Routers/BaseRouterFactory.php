<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Application\Routers;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Schmutzka;
use Schmutzka\Utils\Name;


class BaseRouterFactory
{
	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @inject @var Nette\Caching\Cache */
	public $cache;


	/**
	 * @param  RouteList
	 * @param  array
	 * @param  string
	 * @param  string
	 */
	public function listToRoutes(RouteList $router, $aliasList, $pre = NULL, $post = NULL)
	{
		foreach ($aliasList as $key => $value) {
			$router[] = new Route($pre . $key . $post, $value);
		}

		return $router;
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
