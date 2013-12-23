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

		$title = implode($this->sep, $this->titles);

		if ($this->translator) {
			$title = $this->translator->translate($title);
		}

		return $title;
	}

}
