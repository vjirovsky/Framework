<?php

namespace NewsModule\Components;

use Schmutzka\Application\UI\Module\Grid;


class NewsGrid extends Grid
{
	/** @inject @var Schmutzka\Models\News */
	public $newsModel;


	public function build()
	{
		$this->setPrimaryKey('id');
		$this->addColumn('title', 'Název');
		$this->addColumn('text', 'Text');
		$this->addEditRowAction();
		$this->addDeleteRowAction();
	}

}
