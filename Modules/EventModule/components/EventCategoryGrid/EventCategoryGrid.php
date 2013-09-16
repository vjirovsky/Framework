<?php

namespace EventModule\Components;

use Schmutzka\Application\UI\Module\Grid;


class EventCategoryGrid extends Grid
{
	/** @inject @var Schmutzka\Models\EventCategory */
    public $eventCategoryModel;


	public function build()
    {
		$this->addColumn('name', 'Název');
		$this->addEditRowAction();
		$this->addDeleteRowAction();
    }

}
