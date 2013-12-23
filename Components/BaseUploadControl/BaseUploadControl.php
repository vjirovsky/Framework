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

use Schmutzka;
use Schmutzka\Application\UI\Module\Control;
use UploadHandler;


abstract class BaseUploadControl extends Control
{
	use Schmutzka\Application\UI\TModuleControl;

	public function handleUpload()
	{
		// 1. capture fie upload result
		ob_start();
		$upload_handler = new UploadHandler();
		$jsonData = ob_get_clean();

		$file = json_decode($jsonData)->files[0];

		/** output data example:
		stdClass (6)
			name => 'dnb_typography-1920x1080.jpg' (28)
			size => 366296
			url => 'http://local.peloton.cz/files/dnb_typography-1920x1080.jpg' (58)
			thumbnail_url => 'http://local.peloton.cz/files/thumbnail/dnb_typography-1920x1080.jpg' (68)
			delete_url => 'http://local.peloton.cz/?file=dnb_typography-1920x1080.jpg' (58)
			delete_type => 'DELETE' (6)
		*/

		// 2. file is being processed! custom function this component's in siblings
		$this->processFileUpload($file);

		// 3. clean temp files
		unlink($this->paramService->wwwDir . '/files/' . $file->name);
		unlink($this->paramService->wwwDir . '/files/thumbnail/' . $file->name);
	}


	protected function setupLayoutTemplate()
	{
		$this->template->layoutTemplate = __DIR__ . '/templates/default.latte';
	}

}
