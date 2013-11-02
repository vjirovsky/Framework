<?php

namespace Schmutzka\Diagnostics\Panels;

use Nette;
use Schmutzka\Utils\Filer;


trait CleanerPanelTrait
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
				Filer::emptyFolder($this->paramService->wwwDir . '/webtemp/');

			} elseif ($type == 'session') {
				$this->session->destroy();
				$this->session->start();
			}
		}

		$this->redirect('this');
	}
}
