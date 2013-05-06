<?php

namespace Schmutzka\Application\Routers;

use Nette;
use NotORM;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class ModuleRouterFactory
{
	/** @inject @var NotORM */
	public $database;

	/** @inject @var Nette\Caching\Cache */
	public $cache;

	/** @inject @var Schmutzka\Models\User */
	public $userModel;


	public function createRouter()
	{
		$router = new RouteList();

		$router[] = new Route("index.php", "Front:Homepage:default", Route::ONE_WAY);

		$router[] = new Route("index.php", "Admin:Homepage:default", Route::ONE_WAY);
		$router[] = new Route("<module news|email|admin|page|user|file>/<presenter>/<action>[/<id>]", "Homepage:default");

		$frontRouter = $router[] = new RouteList("Front");
		$frontRouter[] = new Route("<presenter>/<action>[/<id>]", "Homepage:default");

		return $router;
	}

}