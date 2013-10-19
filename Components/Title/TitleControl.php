<?php

namespace Components;

use Schmutzka\Application\UI\Control;


class TitleControl extends Control
{
	/** @var string */
	private $sep = ' | ';

	/** @var bool */
	private $reversed = FALSE;

	/** @var [] */
	private $titles = [];


	protected function renderDefault()
	{
		if ($this->reversed) {
			rsort($this->titles);
		}

		$this->template->title = implode($this->sep, $this->titles);
	}


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

}
