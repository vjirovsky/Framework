<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka;

use Nette;
use Nette\Utils\Strings;
use Schmutzka\Utils\Neon;


class Configurator extends Nette\Configurator
{

	/**
	 * @param bool|string|array
	 */
	public function __construct($debug = NULL)
	{
		parent::__construct();

		$this->parameters = $this->getParameters();

		if ($debug !== NULL) {
			$this->setDebugMode($debug);
		}

		$this->enableDebugger($this->parameters['logDir']);

		// robot loader
		$this->setTempDirectory($this->parameters['appDir'] . '/../temp');
		$this->createRobotLoader()
			->addDirectory($this->parameters['appDir'])
			->addDirectory($this->parameters['libsDir'])
			->register();

		// modules
		$this->registerModules();

		// configs
		$this->addConfig($this->parameters['libsDir'] . '/Schmutzka/configs/default.neon');

		if (Strings::startsWith($_SERVER['HTTP_HOST'], 'dev.')) {
			$name = 'dev';

		} elseif ($this->parameters['environment'] == 'development') {
			$name = 'local';

		} else {
			$name = 'prod';
		}

		$this->loadConfigByName($name);
	}


	/**
	 * @param  array { [ string => string ] }
	 * @param  string
	 */
	public function loadConfigByHost($hostConfigs, $host)
	{
		$configLoaded = FALSE;
		foreach ($hostConfigs as $key => $config) {
			if ($key == $host) {
				$this->addConfig($this->parameters['appDir'] . '/config/' . $config, FALSE);
				$configLoaded = TRUE;
			}
		}

		if ($configLoaded == FALSE) {
			$this->loadConfigByName('local');
		}
	}


	/**
	 * Include paths to directories
	 * @return array
	 */
	private function getParameters()
	{
		$parameters = parent::getDefaultParameters();

		$rootDir = realpath(__DIR__ . '/../../..');
		$parameters['appDir'] = $rootDir . '/app';
		$parameters['libsDir'] =  $rootDir . '/libs';
		$parameters['logDir'] =  $rootDir . '/log';
		$parameters['wwwDir'] =  $rootDir . '/www';
		$parameters['assetsDir'] =  $rootDir . '/libs/Schmutzka/assets';

		return $parameters;
	}


	/**
	 * Add configs of active modules
	 */
	private function registerModules()
	{
		$parameters = Neon::fromFile($this->parameters['appDir'] . '/config/config.neon', 'parameters');

		if (isset($parameters['modules'])) {
			$this->addConfig($this->parameters['appDir'] . '/AdminModule/config.neon');
			foreach ($parameters['modules'] as $module) {
				$moduleDirConfig = ucfirst($module) . 'Module/config.neon';
				if (file_exists($config = $this->parameters['appDir'] . '/' . $moduleDirConfig)) {
					$this->addConfig($config);
				}
			}
		}
	}


	/**
	 * @param  string
	 */
	private function loadConfigByName($name)
	{
		$file = $this->parameters['appDir'] . '/config/config.' . $name . '.neon';
		if (file_exists($file)) {
			$this->addConfig($file, FALSE);

		} else {
			$this->addConfig($this->parameters['appDir'] . '/config/config.neon', FALSE);
		}
	}

}
