<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Components;

use Nette;
use Nette\Utils\Strings;
use Zenify\Application\UI\Form;


trait TTextControl
{

	protected function addFormPerex(Form $form)
	{
		if ($this->moduleParams->perex) {
			$form->addTextarea('perex', 'Perex:')
				->setAttribute('class', 'ckeditor');
		}
	}


	protected function addFormContent(Form $form)
	{
		$form->addTextarea('content', 'Obsah:')
			->setAttribute('class', 'ckeditor');
	}


	/**
	 * @param  array
	 * @return array
	 */
	public function preProcessValues($values)
	{
		$values['url'] = $this->getUniqueUrl($values['title']);
		$values['edited'] = new Nette\DateTime;
		$values['user_id'] = $this->user->id;

		if ($this->id == NULL) {
			$values['created'] = $values['edited'];
		}

		return $values;
	}


	/**
	 * Load content version
	 * @param int
	 */
	public function handleLoadContentVersion($versionId)
	{
		$content = $this->{$this->type . 'ContentModel'}
			->fetch('content');

		$this['form']['content']->setValue($content);
	}


	/**
	 * @param string
	 */
	private function getUniqueUrl($name)
	{
		$url = $originUrl = Strings::webalize($name);
		$i = 1;

		while ($item = $this->{$this->type . 'Model'}->fetch(['url' => $url])) {
			if ($item['id'] == $this->id) {
				return $url;
			}

			$url = $originUrl . '-'. $i;
			$i++;
		}

		return $url;
	}

}
