<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\DI\Extensions;

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
