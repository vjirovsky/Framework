<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

use Nette\Application\UI\Control;
use Nette\Utils\Paginator;


/**
 * @author David Grudl
 */
class VisualPaginator extends Control
{
	/** @persistent @var int */
	public $page = 1;

	/** @var Paginator */
	private $paginator;


	/**
	 * @return Nette\Paginator
	 */
	public function getPaginator()
	{
		if ( ! $this->paginator) {
			$this->paginator = new Paginator;
		}

		return $this->paginator;
	}


	public function render()
	{
		$template = $this->createTemplate()->setFile(__DIR__ . '/templates/default.latte');
		$paginator = $this->getPaginator();
		$page = $paginator->page;
		if ($paginator->pageCount < 2) {
			$steps = array($page);

		} else {
			$arr = range(max($paginator->firstPage, $page - 2), min($paginator->lastPage, $page + 2));
			$count = 2;
			$quotient = ($paginator->pageCount - 1) / $count;

			for ($i = 0; $i <= $count; $i++) {
				$arr[] = round($quotient * $i) + $paginator->firstPage;
			}

			sort($arr);
			$steps = array_values(array_unique($arr));
		}

		$template->steps = $steps;
		$template->paginator = $paginator;
		$template->render();
	}


	/**
	 * Loads state informations.
	 * @param  array
	 * @return void
	 */
	public function loadState(array $params)
	{
		parent::loadState($params);
		$this->getPaginator()->page = $this->page;
	}

}
