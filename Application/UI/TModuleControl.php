<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Application\UI;

use Schmutzka;
use Schmutzka\Utils\Name;


trait TModuleControl
{
	use Schmutzka\Forms\Rendering\TModuleRenderer;

	/** @persistent @var int */
	public $id;

	/** @inject @var Schmutzka\Security\User */
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
			$this->id = $this->model->insert($values);
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
