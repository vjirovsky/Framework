<?php

namespace Components;

use Schmutzka;

/**
 * @method setSep(string)
 * @method getSep()
 * @method setMainTitle(string)
 * @method getMainTitle()
 * @method setMainTitleSep(string)
 * @method getMainTitleSep()
 * @method setAlwaysShowMainTitle(bool)
 * @method getAlwaysShowMainTitle()
 */
class TitleControl extends Schmutzka\Application\UI\Control
{
	/** @var string */
	private $sep = " | ";

	/** @var string */
	private $mainTitleSep = " | ";

	/** @var string */
	private $alwaysShowMainTitle = FALSE;

	/** @var string */
	private $mainTitle;

	/** @var array */
	private $titles = array();


	public function render()
	{
		parent::useTemplate();

		if ($this->isHomepage()) {
			$title = $this->mainTitle;

		} else {
			$title = implode($this->sep, $this->titles);
			if ($this->alwaysShowMainTitle) {
				$title .= ($title ? $this->mainTitleSep : NULL) . $this->mainTitle;
			}
		}

		$this->template->title = $title;
		$this->template->render();
	}


	/**
	 * @param string
	 * @return this
	 */
	public function addTitle($title)
	{
		$this->titles[] = $title;
		return $this;
	}


	/********************** helpers **********************/


	/**
	 * @return bool
	 */
	private function isHomepage()
	{
		$name = $this->getPresenter()->name;
		$action = $this->getPresenter()->action;

		return ($action == "default" && in_array($name, array("Front:Homepage", "Homepage")));
	}

}
