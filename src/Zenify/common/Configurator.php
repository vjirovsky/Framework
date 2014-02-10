<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify;

use Haml;
use Nette;
use Nette\Utils\Strings;
use Zenify;
use WebLoader;


class Configurator extends Nette\Configurator
{

	/**
	 * @param bool|string[]
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
			->addDirectory($this->parameters['appDir'] . '/../vendor/others')
			->register();

		$this->addConfig($this->parameters['appDir'] . '/config/config.neon');
		if (Strings::startsWith($_SERVER['HTTP_HOST'], 'dev.')) {
			$this->loadConfigByName('dev');

		} elseif ($this->parameters['environment'] == 'development') {
			$this->loadConfigByName('local');
		}

		$this->defaultExtensions += [
			'webloader' => 'WebLoader\Nette\Extension',
			'haml' => 'Zenify\Haml\DI\Extension',
			'template' => 'Zenify\TemplateFactory\DI\Extension',
			'Zenify' => 'Zenify\DI\Extensions\ZenifyExtension'
		];
	}


	/**
	 * @return array
	 */
	private function getParameters()
	{
		$parameters = parent::getDefaultParameters();

		$rootDir = realpath(__DIR__ . '/../../../../../..');
		$parameters['appDir'] = $rootDir . '/app';
		$parameters['wwwDir'] =  $rootDir . '/www';
		$parameters['assetsDir'] =  $rootDir . '/libs/Zenify/assets';

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
		}
	}

}
