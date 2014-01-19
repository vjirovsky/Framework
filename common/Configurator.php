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

		$this->enableDebugger($this->parameters['appDir'] . '/../log');

		$this->setTempDirectory($this->parameters['appDir'] . '/../temp');
		$this->createRobotLoader()
			->addDirectory($this->parameters['appDir'])
			->addDirectory($this->parameters['libsDir'])
			->register();

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
	 * @return Compiler
	 */
	protected function createCompiler()
    {
		$compiler = parent::createCompiler();
		$compiler->addExtension(NULL, new DI\Extensions\SchmutzkaExtension);

		return $compiler;
	}


	/**
	 * @return array
	 */
	private function getParameters()
	{
		$parameters = parent::getDefaultParameters();

		$rootDir = realpath(__DIR__ . '/../../..');
		$parameters['appDir'] = $rootDir . '/app';
		$parameters['libsDir'] =  $rootDir . '/libs';
		$parameters['wwwDir'] =  $rootDir . '/www';
		$parameters['assetsDir'] =  $rootDir . '/libs/Schmutzka/assets';

		return $parameters;
	}


	/**
	 * @param  string
	 */
	private function loadConfigByName($name)
	{
		$file = $this->parameters['appDir'] . '/config/config.' . $name . '.neon';
		if (file_exists($file)) {
			$this->addConfig($file);

		} else { // fallback
			$this->addConfig($this->parameters['appDir'] . '/config/config.neon');
		}
	}

}
