<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Components;

use Nette\Utils\Strings;
use Schmutzka\Application\UI\Control;
use Schmutzka\Utils\Name;


class AdminMenuControl extends Control
{
	/** @inject @var Schmutzka\ParamService */
	public $paramService;


	/**
	 * @param string
	 */
	protected function renderDefault($module)
	{
		$moduleParameters = $this->paramService->getModuleParameters($module);

		$items = [];
		foreach ($moduleParameters->menu->items as $item) {
			if ( ! isset($item->cond)) {
				$items[] = $item;

			} elseif ($moduleParameters->{$item->cond}) {
				$items[] = $item;
			}
		}

		$this->template->items = $items;
		$this->template->module = $module;
		$this->template->icon = $moduleParameters->menu->icon;
		$this->template->title = $moduleParameters->title;
	}


	protected function renderTitle()
	{
		$module = $this->presenter->module;
		$moduleParameters = $this->paramService->getModuleParameters($module);

		foreach ($moduleParameters->menu->items as $item) {
			if (Strings::contains(ucfirst($module) . ':' . $item->link, $this->presenter->name)) {
				$this->template->title = $item->label;
				$this->template->subtitle = (isset($item->subtitle) ? $item->subtitle : NULL);
				$this->template->add = (isset($item->add) ? TRUE : NULL);
			}
		}
	}


	protected function renderLangs()
	{
		$moduleParameters = $this->paramService->getModuleParameters('admin');
		if (isset($moduleParameters->availableLocales)) {
			$this->template->locale = $this->presenter->locale;
			$this->template->availableLocales = $moduleParameters->availableLocales;
		}
	}

}
