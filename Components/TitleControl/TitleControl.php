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

use Schmutzka\Application\UI\Control;


class TitleControl extends Control
{
	/** @var string */
	private $sep = ' | ';

	/** @var bool */
	private $reversed = FALSE;

	/** @var [] */
	private $titles = [];


	/**
	 * @param string
	 */
	public function addTitle($title)
	{
		$this->titles[] = $title;
	}


	/**
	 * @param string
	 */
	public function setSep($sep)
	{
		$this->sep = $sep;
	}


	/**
	 * @param bool
	 */
	public function setReversed($reversed)
	{
		$this->reversed = $reversed;
	}


	protected function renderDefault()
	{
		$this->template->title = $this->getFinalTitle();
	}


	protected function renderH1()
	{
		$this->template->title = $this->getFinalTitle();
	}


	/**
	 * @return string
	 */
	private function getFinalTitle()
	{
		if ($this->reversed) {
			rsort($this->titles);
		}

		return implode($this->sep, $this->titles);
	}

}
