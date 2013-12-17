<?php

namespace Components;

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

		if (property_exists($this->presenter, 'translator')) {
			foreach ($flashes as $key => $row) {
				$flashes[$key]->message = $this->presenter->translator->translate($row->message);
			}
		}

		$this->template->flashes = $flashes;
	}

}
