<?php

namespace Components;

use Schmutzka\Application\UI\Form;
use Schmutzka\Application\UI\Control;


class BaseSearchControl extends Control
{
	public $searchModel;

	/** @var string */
	protected $searchColumn = 'name';

	/** @var string */
	protected $searchCaption = 'Filter';

	/** @var array */
	protected $orderBy = [];

	/** @var array */
	private $values;


	protected function createComponentForm()
	{
		$form = new Form;
		$form->addText($this->searchColumn)
			->setAttribute('placeholder', 'What are you looking for?');

		if ($this->orderBy) {
			$form->addSelect('order', NULL, $this->orderBy);
		}

		$form->addSubmit('send', $this->searchCaption)
			->setAttribute('class', 'btn btn-primary');

		return $form;
	}


	public function processForm($form)
	{
		$values = $form->values;

		$result = $this->searchModel->fetchAll();
		if ($values[$this->searchColumn]) {
			$result->where($this->searchColumn . ' LIKE ?', '%' . $values[$this->searchColumn] . '%');
		}

		if ($this->orderBy) {
			$result->order($values['order']);
		}

		$this->values = $values;
		$this->presenter->result = $result;
	}


	public function renderDefault()
	{
		if ($this->values) {
			$this->template->searched = TRUE;
		}
	}


	public function handleReset()
	{
		$this->values = NULL;
		$this->presenter->result = NULL;
		$this->presenter->redirect('this');
	}

}