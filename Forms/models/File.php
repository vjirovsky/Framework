<?php

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
