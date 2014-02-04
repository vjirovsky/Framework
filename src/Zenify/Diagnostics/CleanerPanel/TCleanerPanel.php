<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Diagnostics\Panels;

use Nette;
use Nette\Utils\Finder;


trait TCleanerPanel
{
	/** @inject @var Nette\Caching\Cache */
	public $cache;


	/**
	 * @param  string
	 */
	public function handleRunCleaner($type)
	{
		if ($this->paramService->debugMode) {
			if ($type == 'cache') {
				$this->cache->clean(array(
					Nette\Caching\Cache::ALL => TRUE
				));

			} elseif ($type == 'webtemp') {
				foreach (Finder::find('*')->in($this->paramService->wwwDir . '/webtemp/') as $path => $file) {
					unlink($file);
				}

			} elseif ($type == 'session') {
				$this->session->destroy();
				$this->session->start();
			}
		}

		$this->redirect('this');
	}
}
