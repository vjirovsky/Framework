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


trait TModuleControl
{
	/** @persistent @var int */
	public $id;

	/** @inject @var Schmutzka\Security\User */
	public $user;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;


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
	}


	public function processForm($form)
	{
		if ($this->id && $form['cancel']->isSubmittedBy()) {
			$this->presenter->redirect('default', array('id' => NULL));
		}

		$values = $form->values;

		if ($this->method_exists($this, 'preProcessValues')) {
			$values = $this->preProcessValues($values);
		}

		if ($this->id) {
			$this->model->update($values, $this->id);

		} else {
			$this->id = $this->model->insert($values);
		}

		if ($this->method_exists($this, 'postProcessValues')) {
			$this->postProcessValues($values, $this->id);
		}

		$this->presenter->flashMessage('Uloženo.', 'success');
		$this->presenter->redirect('edit', ['id' => $this->id]);
	}



	/********************** helpers **********************/


	/**
	 * @return  Nette\ArrayHash
	 */
	public function getModuleParams()
	{
		return $this->paramService->getModuleParams($this->presenter->module);
	}


	/**
	 * @return  Models\Base
	 */
	public function getModel()
	{
		$className = $this->getReflection()->getName();
		$classNameParts = explode('\\', $className);
		$modelName = lcfirst(substr(array_pop($classNameParts), 0, -7)) . 'Model';

		return $this->{$modelName};
	}

}
