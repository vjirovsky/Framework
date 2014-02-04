<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Localization;


trait TComponentSimpleTranslator
{

	public function attached($presenter)
	{
		parent::attached($presenter);
		$this['form']->setTranslator($this->translator && $presenter->module == 'front'
			? $this->translator
			: new ComponentSimpleTranslator);
	}

}
