<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Application\UI;

use Zenify;
use Zenify\Utils\Name;


trait TModuleControl
{
	use Zenify\Forms\Rendering\TModuleRenderer;

	/** @persistent @var int */
	public $id;

	/** @inject @var Zenify\Security\User */
	public $user;


	public function attached($presenter)
	{
		parent::attached($presenter);

		if (($this->id || (property_exists($presenter, 'id') && $this->id = $presenter->id)) && isset($this['form'])) {
			$this['form']['send']->caption = 'Uložit';
			$this['form']['send']
				->setAttribute('class', 'btn btn-success');

			$this['form']->addSubmit('cancel', 'Zrušit')
				->setAttribute('class', 'btn btn-default')
				->setValidationScope(FALSE);

			$defaults = $this->model->fetch($this->id);
			if ($this->method_exists($this, 'preProcessDefaults')) {
				$defaults = $this->preProcessDefaults($defaults);
			}

			$this['form']->setDefaults($defaults);
		}

		$this->setupModuleRenderer($this['form']);
	}


	public function processForm($values, $form)
	{
		if ($form->submitName == 'cancel') {
			$this->presenter->redirect('default', ['id' => NULL]);
		}

		$values = $this->preProcessValues($values);
		if ($this->id) {
			$this->model->update($values, $this->id);

		} else {
			$row = $this->model->insert($values);
			$this->id = $row['id'];
		}

		$this->postProcessValues($values, $this->id);

		$this->presenter->flashMessage('Uloženo.', 'success');
		$this->presenter->redirect('edit', ['id' => $this->id]);
	}


	/**
	 * @return  Nette\ArrayHash
	 */
	public function getModuleParameters()
	{
		return $this->paramService->getModuleParameters($this->presenter->module);
	}


	/**
	 * @return  Models\Base
	 */
	public function getModel()
	{
		$modelName = Name::modelFromControlReflection($this->getReflection());
		return $this->{$modelName};
	}


	/**
	 * @param  []
	 * @return []
	 */
	protected function preProcessValues($values)
	{
		return $values;
	}


	/**
	 * @param  []
	 * @param  int
	 */
	protected function postProcessValues($values, $id)
	{

	}

}
