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

use Zenify;
use Zenify\Application\UI\Control;


class FlashMessageControl extends Control
{

	protected function renderDefault()
	{
		$flashes = $this->parent->template->flashes;

		if ($this->translator && $this->presenter->module == 'front') {
			foreach ($flashes as $key => $row) {
				$flashes[$key]->message = $this->translator->translate($row->message);
			}
		}

		$this->template->flashes = $flashes;
	}

}
