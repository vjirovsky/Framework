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

use Haml;
use Nette;
use Nette\Utils\Strings;
use Schmutzka;
use Webloader;


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
			->addDirectory($this->parameters['appDir'] . '/../libs')
			->register();

		$this->addConfig($this->parameters['appDir'] . '/config/config.neon');
		if (Strings::startsWith($_SERVER['HTTP_HOST'], 'dev.')) {
			$this->loadConfigByName('dev');

		} elseif ($this->parameters['environment'] == 'development') {
			$this->loadConfigByName('local');
		}
	}


	/**
	 * @return Compiler
	 */
	protected function createCompiler()
	{
		$compiler = parent::createCompiler()
			->addExtension('webloader', new Webloader\Nette\Extension)
			->addExtension('haml', new Haml\DI\Extension)
			->addExtension('template', new Schmutzka\DI\Extensions\TemplateExtension)
			->addExtension('schmutzka', new Schmutzka\DI\Extensions\SchmutzkaExtension);

		if (class_exists('Models\User')) {
			$compiler->addExtension('database', new Schmutzka\DI\Extensions\DatabaseExtension);
		}
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
		}
	}

}
