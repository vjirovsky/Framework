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

use Schmutzka;
use Schmutzka\Application\UI\Control;


class FlashMessageControl extends Control
{

	protected function renderDefault()
	{
		$flashes = $this->parent->template->flashes;
		if ( ! count($flashes)) {
			return NULL;
		}

		if ($this->translator) {
			foreach ($flashes as $key => $row) {
				$flashes[$key]->message = $this->translator->translate($row->message);
			}
		}

		$this->template->flashes = $flashes;
	}

}
