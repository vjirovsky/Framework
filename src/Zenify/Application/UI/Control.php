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
use Nette\Utils\Strings;
use Zenify;


abstract class Control extends Nette\Application\UI\Control
{
	use Zenify\TemplateFactory\Templating\TTemplateFactory;
	use TCreateComponent;

	/** @var Nette\Localization\ITranslator */
	protected $translator;

	/** @inject @var Zenify\ParamService */
	public $paramService;


	public function __construct(Nette\Localization\ITranslator $translator = NULL)
	{
		$this->translator = $translator;
	}


	/**
	 * Rendering view
	 * @param  string
	 * @param  array
	 */
	public function __call($name, $args)
	{
		if (Strings::startsWith($name, 'render')) {
			if ($name == 'render') {
				$view = 'default';

			} else {
				$view = lcfirst(substr($name, 6));
			}

			$this->setTemplateSource($this->template, $view);

			if ($this->translator) {
				$this->template->setTranslator($this->translator);
			}

			// calls $this->render<View>()
			$renderMethod = 'render' . ucfirst($view);
			if (method_exists($this, $renderMethod)) {
				call_user_func_array(array($this, $renderMethod), $args);
			}

			$this->template->render();
		}
	}


	/**
	 * @param Nette\Templating\Template
	 * @param string
	 */
	private function setTemplateSource($template, $view)
	{
		// 1. in-app
		$dir = $this->paramService->appDir . ($this->presenter->module
			? '/' . ucfirst($this->presenter->module) . 'Module'
			: '/FrontModule') . '/templates/components/';

		$name = substr(strrchr(get_class($this), '\\'), 1);
		$path = $dir . lcfirst($name) . '.latte';

		if (file_exists($path)) {
			$template->setFile($path);
			return;
		}

		// 2. in-component
		$dir = dirname($this->getReflection()->getFileName());
		$path = $dir . '/templates/' . $view . '.latte';

		if (file_exists($path)) {
			$template->setFile($path);
			return;
		}

		// 3. base fallback
		if (Strings::endsWith(get_class($this), 'Grid')) {
			$template->setFile(__DIR__. '/templates/grid.latte');

		} else {
			$template->setFile(__DIR__. '/templates/control.latte');
		}
	}


	/**
	 * @param  string
	 * @param  string
	 * @param  string
	 * @param  Html
	 */
	public function buildConditionsLink($text, $node, $path)
	{
		if ($this->translator) {
			$text = $this->translator->translate($text);
			$node = $this->translator->translate($node);
		}

		$a = Html::el('a')
			->setText($node)
			->target('_blank')
			->href($this->presenter->link($path));

		return Html::el('span')->setHtml($text . $a);
	}

}
