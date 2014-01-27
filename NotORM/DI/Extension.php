<?php

namespace Schmutzka\NotORM\DI;

use Nette;
use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;


class Extension extends CompilerExtension
{
	/** @var array */
	private $defaults = array(
		'driver' => 'mysql',
		'username' => 'root',
		'password' => NULL,
		'host' => 'localhost',
		'cache' => 'file',
		'filename' => 'notorm.cache',
		'rowClass' => 'NotORM_Row'
	);


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$builder->addDefinition($this->prefix('pdo'))
			->setClass('PDO', [
				$config['driver'] . ':host=' . $config['host'] . ';dbname=' . $config['dbname'],
				$config['username'],
				$config['password']
			])
			->addSetup('query', ['SET NAMES utf8']);

		if ($config['cache'] == 'file') {
			$builder->addDefinition($this->prefix('cache'))
				->setClass('NotORM_Cache_File', array(__DIR__ . '/../../../temp/' . $config['filename']));
		}

		$builder->addDefinition($this->prefix('database'))
			->setClass('NotORM', [$this->prefix('@pdo'), NULL, $config['cache'] ? $this->prefix('cache') : NULL]);

		$builder->addDefinition($this->prefix('panel'))
			->setFactory('Schmutzka\NotORM\Diagnostics\Panel::getInstance');
	}


	public function afterCompile(ClassType $class)
	{
		parent::afterCompile($class);

		$config = $this->getConfig($this->defaults);

		$init = $class->methods['initialize'];
		$init->addBody('Nette\Diagnostics\Debugger::getBar()->addPanel($this->createServiceNotorm__panel());');
		$init->addBody('$notormPanel = $this->getService(?);', array($this->prefix('panel')));
		$init->addBody('$this->getService(?)->debug = function($query, $params) use ($notormPanel) {
				return $notormPanel->logQuery($query, $params);
			};', array($this->prefix('database'))
		);

		if ($config['rowClass'] != 'NotORM_Row') {
			$init->addBody('$this->getService(?)->rowClass = ?;', array($this->prefix('database'), $config['rowClass']));
		}
	}

}
