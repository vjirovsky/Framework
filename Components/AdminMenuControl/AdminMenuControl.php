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

use Nette;
use Nette\Utils\Strings;
use Schmutzka;
use Schmutzka\Application\UI\Control;


class AdminMenuControl extends Control
{
	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @inject @var Schmutzka\Security\User */
	public $user;


	/**
	 * @param string
	 */
	protected function renderDefault($module)
	{
		$moduleParameters = $this->getModuleParameters($module);

		$items = [];
		if (isset($moduleParameters->menu) && isset($moduleParameters->menu->items)) {
			foreach ($moduleParameters->menu->items as $item) {
				if ( ! isset($item->cond)) {
					$items[] = $item;

				} elseif ($moduleParameters->{$item->cond}) {
					$items[] = $item;
				}
			}

			$this->template->icon = $moduleParameters->menu->icon;
			$this->template->items = $items;
		}

		$this->template->module = $module;
		$this->template->title = $moduleParameters->title;
	}


	protected function renderTitle()
	{
		$module = $this->presenter->module;
		$moduleParameters = $this->getModuleParameters($module);

		$view = $this->presenter->view;
		$title = '';

		if ($view == 'add') {
			$link = substr($this->presenter->name, strlen($module) + 1);

			if (isset($moduleParameters->menu->items)) {
				foreach ($moduleParameters->menu->items as $item) {
					if (Strings::contains($item->link, $link)) {
						$title = $item->label;
					}
				}

			} else {
				$title = $moduleParameters->title;
			}

			$title .= ' - nová položka';

		} elseif (Strings::startsWith($view, 'edit')) {
			$item = $this->presenter->template->item;
			$title = 'Úprava položky' .
				(isset($item['title']) ? ': ' . $item['title'] :
					(isset($item['name']) ? ': ' . $item['name'] :
						(isset($item['login']) ? ': ' . $item['login'] :
					NULL)));

		} elseif (isset($moduleParameters->menu->items)) {
			$link = substr($this->presenter->name . ':' . $view, strlen($module) + 1);
			foreach ($moduleParameters->menu->items as $item) {
				if ($item->link == $link) {
					$title = $item->label;
				}
			}

		} else {
			$title = $moduleParameters->title;
		}

		$this->template->title = $title;
	}


	/**
	 * @param  string
	 * @return []
	 */
	private function getModuleParameters($module)
	{
		return $this->paramService->getModuleParameters($module);
	}

}
