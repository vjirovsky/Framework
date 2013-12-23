<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Models;

use Nette\Utils\MimeTypeDetector;
use Schmutzka\Models\Base;


class File extends Base
{
	/** @inject @var Schmutzka\ParamService */
	public $paramService;


	/**
	 * @param  int
	 */
	public function download($id)
	{
		$file = $this->fetch($id);
		$filePath = $this->paramService->wwwDir . '/storage/files/' . $file['name'];

		if (file_exists($filePath)) {
			$content = file_get_contents($filePath);
			header('Content-type: ' . $file['type'] ?: MimeTypeDetector::fromString($content));
			header('Content-Disposition: attachment; filename="' . $file['name_origin'] . '"');
			readfile($filePath);
		}

		die;
	}

}
