<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Components;

use Kdyby;
use Nette\Utils\Strings;
use Zenify\Application\UI\Control;
use Zenify\Utils\Name;


class AdminMenuControl extends Control
{
	/** @inject @var Zenify\ParamService */
	public $paramService;

	/** @inject @var Kdyby\Translation\LocaleResolver\SessionResolver */
	public $sessionResolver;


	/**
	 * @param string
	 */
	public function handleSetLocale($locale)
	{
		$this->sessionResolver->setLocale($locale);
		$this->presenter->redirect('this');
	}


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
			$this->template->locale = $this->translator->getLocale();
			$this->template->availableLocales = $moduleParameters->availableLocales;
		}
	}

}
