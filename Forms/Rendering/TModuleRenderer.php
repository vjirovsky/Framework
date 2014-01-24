<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Forms\Rendering;

use Nette;
use Nette\Forms\Form;


trait TModuleRenderer
{

	public function setupModuleRenderer(Form $form)
	{
		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = 'div class="module-body"';
		$renderer->wrappers['pair']['container'] = 'div class=control-group';
		$renderer->wrappers['control']['container'] = 'div class=controls';
		$renderer->wrappers['label']['container'] = 'div class="control-label"';

		$form->getElementPrototype()->class('form-horizontal module');
	}

}
