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

use Nette;
use Nette\Utils\Html;
use Nette\Utils\Validators;
use Zenify;
use Zenify\Doctrine\Utils;
use Zenify\Forms\Controls;


/**
 * @method setProcessor(callable)
 */
class Form extends Nette\Application\UI\Form
{
	use Zenify\Forms\TFileUpload;
	use Zenify\Forms\TOnSuccessCallback;
	use Zenify\Forms\Rendering\TBootstrapRenderer;

	/** validators */
	const DATE = 'Zenify\Forms\Rules::validateDate';
	const TIME = 'Zenify\Forms\Rules::validateTime';
	const EXTENSION = 'Zenify\Forms\Rules::extension';

	/** @var string */
	public $csrfProtection = 'Prosím odešlete formulář znovu, vypršel bezpečnostní token.';

	/** @inject @var Zenify\ParamService */
	public $paramService;

	/** @var callable */
	protected $processor;

	/** @var bool */
	private $isBuilt = FALSE;


	/**
	 * @param string
	 * @param int
	 */
	public function __set($name, $value)
	{
		if (in_array($name, ['id', 'class', 'target', 'ajax'])) {
			$this->elementPrototype->$name = $value;
		}
	}


	/**
	 * beforeRender build function
	 */
	public function build()
	{
		$this->isBuilt = TRUE;

		if ($this->csrfProtection) {
			$this->addProtection($this->csrfProtection);
		}
	}


	/**
	 * @param string
	 * @param string
	 */
	public function moveBefore($name, $where)
	{
		if ( ! $this->isBuilt) {
			$this->build();
		}

		$component = $this->getComponent($name);
		$this->removeComponent($component);
		$this->addComponent($component, $name, $where);
	}


	/**
	 * @param array|object
	 * @param bool
	 * @return self
	 */
	public function setDefaults($defaults, $erase = FALSE)
	{
		if (is_object($defaults)) {
			$defaults = Utils::toArray($defaults);
		}

		return parent::setDefaults($defaults, $erase);
	}


	/**
	 * @param string
	 * @param string|NULL
	 */
	public function addToggleGroup($id, $label = NULL)
	{
		$fieldset = Html::el('fieldset')->id($id)
			->style('display:none');

		$this->addGroup($label)
			->setOption('container', $fieldset);
	}


	/**
	 * Is called when the component becomes attached to a monitored object
	 * @param Nette\Application\IComponent
	 */
	protected function attached($presenter)
	{
		parent::attached($presenter);
		$this->attachHandlers($presenter);

		if (property_exists($presenter, 'translator')) {
			$this->setTranslator($presenter->translator);
		}

		if ( ! $this->isBuilt) {
			$this->build();
		}

		// @todo annotation refactoring
		if (($presenter->module == 'front' && isset($presenter->paramService->useBootstrapFront)) || isset($this->presenter->paramService->useBootstrap)) {
			$form = $this;
			$this->setupBootstrapRenderer($form);
		}
	}


	/**
	 * @param Nette\Application\UI\Presenter
	 */
	protected function attachHandlers($presenter)
	{
		$processMethodName = 'process' . lcfirst($this->getName());

		if (method_exists($this->parent, $processMethodName)) {
			$this->onSuccess[] = [$this->parent, $processMethodName];
		}
	}


	/**
	 * @param string
	 */
	public function addError($message)
	{
		$this->valid = FALSE;
		$this->presenter->flashMessage($message, 'danger');
	}


	/**
	 * @param  bool
	 * @return  []|ArrayHash
	 */
	public function getValues($asArray = TRUE)
	{
		$values = parent::getValues($asArray);

		$processorMethod = lcfirst($this->getName()) . 'Processor';
		if (method_exists($this->parent, $processorMethod) && is_callable($this->parent->$processorMethod)) {
			$values = call_user_func($this->parent->$processorMethod, $values);
		}

		$this->processFileUploads($values, $this);

		return $values;
	}


	/**
	 * @return string
	 */
	public function getSubmitName()
	{
		return $this->isSubmitted()->name;
	}


	/**
	 * @param  string
	 * @param  string
	 * @param  bool
	 * @return  Zenify\Forms\Controls\UploadControl
	 */
	public function addUpload($name, $label = NULL, $multiple = FALSE)
	{
		return $this[$name] = new Zenify\Forms\Controls\UploadControl($label, $multiple);
	}


	/**
	 * @param  string
	 * @param  string|NULL
	 * @return  Nette\Forms\Controls\TextInput
	 */
	public function addUrl($name, $label = NULL)
	{
		$control = $this[$name] = new Nette\Forms\Controls\TextInput($label);
		$control->addFilter(function ($value) {
			return (Validators::isUrl($value) || $value == NULL) ? $value : 'http://' . $value;
		})->addCondition(Form::FILLED)
			->addRule(Form::URL, 'Opravte adresu odkazu, aby začínal na http://');

		return $control;
	}


	/**
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return  Zenify\Forms\Controls\IconSubmitButton
	 */
	public function addIconSubmitButton($name, $label, $iconClass)
	{
		return $this[$name] = new Zenify\Forms\Controls\IconSubmitButton($label, $iconClass);
	}

}
