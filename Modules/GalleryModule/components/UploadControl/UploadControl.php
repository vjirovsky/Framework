<?php

namespace GalleryModule\Components;

use Nette;
use Nette\Image;
use Nette\Utils\Finder;
use Schmutzka\Utils\Filer;
use Schmutzka\Components\BaseUploadControl;
use Schmutzka\Application\UI\Control;
use UploadHandler;


class UploadControl extends BaseUploadControl
{
	/** @inject @var Schmutzka\Models\Gallery */
	public $galleryModel;

	/** @inject @var Schmutzka\Models\GalleryFile */
	public $galleryFileModel;


	public function processFileUpload($file)
	{
		$image = Nette\Image::fromFile($file->url);

		// 1. save, resize and save
		$uniqueName = Filer::getUniqueName($this->galleryDir, $file->name);

		if ( ! is_dir($this->galleryDir)) {
			mkdir($this->galleryDir, 0777);
		}

		foreach ($this->moduleParams->sizeVersions as $type => $dimensions) {
			if ($type == 'natural') {
				$image->resize($dimensions['width'], $dimensions['height'], Image::SHRINK_ONLY);
				$image->save($this->galleryDir . '/' . $uniqueName);

			} else {
				Filer::resizeToSubfolder($image, $this->galleryDir, $dimensions['width'], $dimensions['height'], $uniqueName);
			}
		}

		// 2. save to db
		$data = [
			'gallery_id' => $this->id,
			'name' => $uniqueName,
			'name_orig' => $file->name,
		];

		$this->galleryFileModel->insert($data);
	}


	public function handleSort()
	{
		$data = explode(',', $_POST['data']);
		$i = 1;
		foreach ($data as $item) {
			$this->galleryFileModel->update(['rank' => $i], $item);
			$i++;
		}
	}


	/**
	 * @param int
	 */
	public function handleDeleteFile($fileId)
	{
		if ($galleryFile = $this->galleryFileModel->item($fileId)) {
			foreach (Finder::findFiles($galleryFile['name'])->from($this->galleryDir) as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}

			$this->galleryFileModel->delete($fileId);
			$galleryItem = $this->galleryModel->item($this->id);

			$this->presenter->flashMessage('Záznam byl úspěšně smazán.','success');

		} else {
			$this->presenter->flashMessage('Tento záznam neexistuje.', 'error');
		}

		$this->presenter->redirect('this', array('fileId' => NULL));
	}


	protected function renderDefault()
	{
		$key = array(
			'gallery_id' => $this->id
		);
		$this->template->galleryThumbDir = $this->getGalleryDir(FALSE) . 'h100/';
		$this->template->galleryFiles = $this->galleryFileModel->fetchAll($key)->order('rank, id');

		parent::setupLayoutTemplate();
	}


	/********************** helpers **********************/


	/**
	 * @param bool
	 */
	public function getGalleryDir($absolute = TRUE)
	{
		return ($absolute ? $this->paramService->wwwDir : '') . '/files/gallery/' . $this->id . '/';
	}

}
