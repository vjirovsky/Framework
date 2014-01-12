<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Forms;

use Nette;


trait TOnSuccessCallback
{

	/**
	 * Fires submit/click events.
	 * @return void
	 */
	public function fireEvents()
	{
		if ( ! $this->isSubmitted()) {
			return;
		}

		$this->validate();

		if ($this->onSuccess) {
			foreach ($this->onSuccess as $handler) {
				if (!$this->isValid()) {
					$this->onError($this);
					break;
				}
				Nette\Utils\Callback::invoke($handler, $this->getValues(), $this);
			}

		} elseif ( ! $this->isValid()) {
			$this->onError($this);
		}

		$this->onSubmit($this);
	}

}
