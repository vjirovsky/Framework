<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Forms\Controls;

use Nette\Forms\Controls\Button;
use Nette\Utils\Html;


/**
 * @method setIconPosition(string)
 */
class IconSubmitButton extends Button
{
	/** @var string */
	protected $iconPosition = 'left';

	/** @var string */
	private $iconClass;


	/**
	 * @param  string
	 * @param  string
	 */
	public function __construct($caption, $iconClass)
	{
		parent::__construct($caption);
		$this->iconClass = $iconClass;
	}


	/**
	 * @param  string
	 * @return Html
	 */
	public function getControl($caption = NULL)
	{
		$control = parent::getControl($caption);
		$control->setType('submit');
		$control->setName('button');

		$icon = Html::el('i')->setClass($this->iconClass);

		if ($this->iconPosition == 'left') {
			$label = $icon . $control->value;

		} else {
			$label = $control->value . $icon;
		}

		$control->setHtml($label);

		return $control;
	}

}
