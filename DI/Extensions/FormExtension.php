<?php

namespace Schmutzka\DI\Extensions;

use Nette;
use Nette\Forms\Container;
use Nextras\Forms\Controls\DatePicker;
use Nextras\Forms\Controls\DateTimePicker;
use Nextras\Forms\Controls\MultiOptionList;


class FormExtension extends Nette\Object
{

	public static function register()
	{
		Container::extensionMethod('addDatePicker', function(Container $container, $name, $label = NULL) {
			$control = new DatePicker($label);
			$container[$name] = $control;

			return $control;
		});

		Container::extensionMethod('addDateTimePicker', function(Container $container, $name, $label = NULL) {
			$control = new DateTimePicker($label);
			$container[$name] = $control;

			return $control;
		});
	}

}
