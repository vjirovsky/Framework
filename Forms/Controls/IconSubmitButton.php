<?php

namespace Schmutzka\Forms\Controls;

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
