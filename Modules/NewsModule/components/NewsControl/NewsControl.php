<?php

namespace NewsModule\Components;

use Nette;
use Schmutzka\Application\UI\Form;
use Schmutzka\Application\UI\Module\Control;


class NewsControl extends Control
{
	/** @inject @var Schmutzka\Models\News */
	public $newsModel;


	public function createComponentForm()
	{
		$form = new Form;

		$form->addText('title', 'Název:')
			->addRule(Form::FILLED, 'Zadejte název novinky');
		$form->addTextarea('text', 'Obsah:')
			->addRule(Form::FILLED, 'Zadejte text novinky');
		$form->addSubmit('send', 'Uložit')
			->setAttribute('class', 'btn btn-primary');

		return $form;
	}


	/**
	 * @param  array
	 * @return array
	 */
	public function preProcessValues($values)
	{
		$values['created'] = new Nette\DateTime;
		$values['user_id'] = $this->user->id;
		return $values;
	}

}
