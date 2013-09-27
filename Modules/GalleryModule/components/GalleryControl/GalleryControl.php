<?php

namespace GalleryModule\Components;

use Nette;
use Schmutzka;
use Schmutzka\Application\UI\Module\Control;
use Schmutzka\Application\UI\Form;


class GalleryControl extends Control
{
	/** @inject @var Schmutzka\Models\Gallery */
	public $galleryModel;


	public function createComponentForm()
    {
		$form = new Form;
		$form->addText('name', 'Název:')
			->addRule(Form::FILLED, 'Povinné');

		if ($this->moduleParams->description) {
			$form->addTextarea('description', 'Popis:')
				->setAttribute('class', 'span8');
		}

		$form->addSubmit('send', 'Uložit')
			->setAttribute('class', 'btn btn-primary');

		if ($this->id) {
			$defaults = $this->galleryModel->item($this->id);
			$form->setDefaults($defaults);
		}

		return $form;
	}


	/**
	 * @param array
	 * @return array
	 */
	public function preProcessValues($values)
	{
		$values['edited'] = new Nette\DateTime;
		$values['user_id'] = $this->user->id;

		return $values;
	}

}
