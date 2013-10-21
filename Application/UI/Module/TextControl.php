<?php

namespace Schmutzka\Application\UI\Module;

use Nette;
use Nette\Utils\Strings;
use Schmutzka;
use Schmutzka\Application\UI\Module\Control;
use Schmutzka\Application\UI\Form;


abstract class TextControl extends Control
{
	/** @var string (article|page) */
	protected $type;

	/** @var Schmutzka\Models\Gallery */
	protected $galleryModel;


	public function injectModels(Schmutzka\Models\Gallery $galleryModel = NULL) {
		$this->galleryModel = $galleryModel;
	}


	/********************** form parts **********************/


	/**
	 * @param Nette\Application\UI\Form
	 */
	protected function addFormPerex(Form $form)
	{
		if ($this->moduleParams->perex) {
			$form->addTextarea('perex', 'Perex:')
				->setAttribute('class', 'ckeditor');
		}
	}


	/**
	 * @param Nette\Application\UI\Form
	 */
	protected function addFormContent(Form $form)
	{
		$form->addTextarea('content', 'Obsah:')
			->setAttribute('class', 'ckeditor');
	}


	/**
	 * @param Nette\Application\UI\Form
	 */
	protected function addFormAttachments($form)
	{
		if ($this->moduleParams->attachmentGallery) {
			$form->addGroup('Přílohy');

			$galleryList = $this->galleryModel->fetchPairs('id', 'name');
			$form->addSelect('gallery_id', 'Připojená galerie', $galleryList)
				->setPrompt($galleryList ? 'Vyberte' : 'Zatím neexistuje žádná fotogalerie');
		}
	}


	/********************** process form **********************/


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
	 * @param  array
	 * @param  id
	 */
	public function postProcessValues($values, $id)
	{
		$this->postProcessValuesSaveContentHistory($values, $id);
	}


	/**
	 * @param  array
	 * @param  int
	 */
	private function postProcessValuesSaveContentHistory($values, $id)
	{
		if ($this->moduleParams->contentHistory) {
			$array = array(
				'content' => $values['content'],
				$this->type . '_id' => $id,
				'user_id' => $this->user->id,
				'edited' => new Nette\DateTime
			);

			$this->{$this->type . 'ContentModel'}->insert($array);
		}
	}


	/**
	 * Load content version
	 * @param int
	 */
	public function handleLoadContentVersion($versionId)
	{
		$this['form']['content']->setValue($this->{$this->type . 'ContentModel'}->fetch('content'));
	}



	/********************** render **********************/


	/**
	 * @param  string
	 */
	protected function loadTemplateValues()
	{
		if ($this->id) {
			if ($this->moduleParams->contentHistory) {
				$this->template->contentHistory = $this->{$this->type . 'ContentModel'}->fetchAll(array($this->type . '_id' => $this->id))
					->select('user.login login, ' . $this->type . '_content.*')
					->order('edited DESC');
			}
		}
	}


	/********************** helpers **********************/


	/**
	 * @param string
	 */
	private function getUniqueUrl($name)
	{
		$url = $originUrl = Strings::webalize($name);
		$i = 1;

		while ($item = $this->{$this->type . 'Model'}->fetch(['url' => $url]) {
			if ($item['id'] == $this->id) {
				return $url;
			}

			$url = $originUrl . '-'. $i;
			$i++;
		}

		return $url;
	}

}
