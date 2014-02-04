<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Application\Routers;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


trait TUtils
{

	/**
	 * @param  RouteList
	 * @param  array
	 * @param  string
	 * @param  string
	 * @return  RouteList
	 */
	public function listToRoutes(RouteList $router, $aliasList, $pre = NULL, $post = NULL)
	{
		foreach ($aliasList as $key => $value) {
			$router[] = new Route($pre . $key . $post, $value);
		}

		return $router;
	}

}
