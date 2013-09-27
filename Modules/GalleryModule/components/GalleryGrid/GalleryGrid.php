<?php

namespace GalleryModule\Components;

use Schmutzka\Application\UI\Module\Grid;


class GalleryGrid extends Grid
{
	/** @inject @var Schmutzka\Models\Gallery */
	public $galleryModel;


	public function build()
	{
		$this->addColumn('name', 'Název');
		if ($this->moduleParams->description) {
			$this->addColumn('description', 'Popisek');
		}
		$this->addColumn('created', 'Vytvořeno');
		$this->addEditRowAction();
		$this->addDeleteRowAction();
	}

}
