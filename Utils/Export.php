<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Utils;

use Nette;


class Export extends Nette\Object
{
	/** @var string */
	public $sep = ';';

	/** @var string */
	public $fileName;

	/** @var Nette\Application\UI\Presenter */
	private $presenter;


	/**
	 * @param Nette\Application\UI\Presenter
	 */
	public function __construct(Nette\Application\UI\Presenter $presenter)
	{
		$this->presenter = $presenter;
	}


	/**
	 * CVS export
	 * @param array
	 * @param array
	 */
	public function cvs($fields, $data)
	{
		$file = __DIR__ . '/Export/cvs.latte';

		header('Content-Type: application/csv, windows-1250');
		header('Content-Disposition: attachment;filename='' . $this->fileName . '.csv'');
		header('Cache-Control: max-age=0');

		$template = $this->presenter->createTemplate()->setFile($file);
		$template->fields = $fields;
		$template->data = $data;
		$template->sep = $this->sep;

		$template->render();
		exit();
	}


	/**
	 * Iconv shortcut - alternative to helper
	 */
	private function ic($value, $from = 'utf-8', $to = 'windows-1250')
	{
		return iconv($from, $to, $value);
	}

}
