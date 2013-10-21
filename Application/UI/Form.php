<?php

namespace Schmutzka\Application\UI;

use Kdyby\BootstrapFormRenderer\BootstrapRenderer;
use Nette;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;
use Nette\Utils\Validators;
use Schmutzka\Forms\Controls;
use Schmutzka\Forms\FileUploadTrait;


/**
 * @method setProcessor(callable)
 */
class Form extends Nette\Application\UI\Form
{
	use FileUploadTrait;

	/** validators */
	const DATE = 'Schmutzka\Forms\Rules::validateDate';
	const TIME = 'Schmutzka\Forms\Rules::validateTime';
	const EXTENSION = 'Schmutzka\Forms\Rules::extension';

	/** @var string */
	public $csrfProtection = 'Prosím odešlete formulář znovu, vypršel bezpečnostní token.';

	/** @var bool */
	public $useBootstrap = TRUE;

	/** @inject @var Nette\Localization\ITranslator */
	public $translator;

	/** @inject @var Schmutzka\ParamService */
	public $paramService;

	/** @inject @var Schmutzka\Models\File */
	public $fileModel;

	/** @var callable */
	protected $processor;

	/** @var bool */
	private $isBuilt = FALSE;


	/**
	 * BeforeRender build function
	 */
	public function build()
	{
		$this->isBuilt = TRUE;

		if ($this->csrfProtection) {
			$this->addProtection($this->csrfProtection);
		}
	}


	/**
	 * Changes position of control
	 * @param string
	 * @param string
	 */
	public function moveBefore($name, $where)
	{
		if (!$this->isBuilt) {
			$this->build();
		}

		$component = $this->getComponent($name);
		$this->removeComponent($component);
		$this->addComponent($component, $name, $where);
	}


	/**
	 * Set defaults accepts array, object or empty string
	 * @param array|object
	 * @param bool
	 * @return  this
	 */
	public function setDefaults($defaults, $erase = FALSE)
	{
		if ($defaults instanceof NotORM_Row) {
			$defaults = $defaults->toArray();
		}

		parent::setDefaults($defaults, $erase);

		return $this;
	}


	/**
	 * Flash message error
	 * @param string
	 */
	public function addError($message)
	{
		// $this->valid = FALSE;
		$this->presenter->flashMessage($message, 'error');
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

		if (!$this->isBuilt) {
			$this->build();
		}

		if (method_exists($this, 'afterBuild')) {
			$this->afterBuild();
		}

		if ($this->translator) {
			$this->setTranslator($this->translator);
		}

		if ($presenter instanceof Nette\Application\IPresenter) {
			$this->attachHandlers($presenter);
			$this->paramService = $presenter->paramService;

			if (property_exists($presenter, 'fileModel')) {
				$this->fileModel = $presenter->fileModel;
			}
		}

		if (($presenter->module != 'front' && $this->useBootstrap) || isset($this->presenter->paramService->forms->useBootstrap)) {
			$this->setRenderer(new BootstrapRenderer($presenter->template));
		}
	}


	/**
	 * Automatically attach methods
	 * @param Nette\Application\UI\Presenter
	 */
	protected function attachHandlers($presenter)
	{
		$processMethodName = 'process' . lcfirst($this->getName());

		if (method_exists($this->parent, $processMethodName)) {
			$this->onSuccess[] = callback($this->parent, $processMethodName);
		}
	}


	/**
	 * @param  bool
	 * @return  []|ArrayHash
	 */
	public function getValues($asArray = TRUE)
	{
		$values = parent::getValues($asArray);
		if ($this->processor && is_callable($this->processor)) {
			$values = call_user_func($this->processor, $values);

		} elseif (method_exists($this->parent, lcfirst($this->getName()) . 'Processor') && is_callable($this->processor)) {
			$values = call_user_func($this->processor, $values);
		}

		$this->processFileUploads($values);

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
	 * @return  Controls\UploadControl
	 */
	public function addUpload($name, $label = NULL, $multiple = FALSE)
	{
		return $this[$name] = new Controls\UploadControl($label, $multiple);
	}


	/**
	 * @param  string
	 * @param  string
	 * @param  string
	 * @return Controls\AntispamControl
	 */
	public function addAntispam($name = 'antispam', $label = 'Toto pole vymažte.', $msg = 'Byl detekován pokus o spam')
	{
		return $this[$name] = new Controls\AntispamControl($label, NULL, NULL, $msg);
	}


	/**
	 * @param string
	 * @param string|NULL
	 * @param array
	 * @return  Controls\SuggestControl
	 */
	public function addSuggest($name, $label = NULL, $suggestList)
	{
		return $this[$name] = new Controls\SuggestControl($label, $suggestList);
	}


	/**
	 * @param  string
	 * @param  string|NULL
	 * @return  TextInput
	 */
	public function addUrl($name, $label = NULL)
	{
		$control = $this[$name] = new TextInput($label);
		$control->addFilter(function ($value) {
			return (Validators::isUrl($value) || $value == NULL) ? $value : 'http://' . $value;
		})->addCondition(Form::FILLED)
			->addRule(Form::URL, 'Opravte adresu odkazu');

		return $control;
	}

}
