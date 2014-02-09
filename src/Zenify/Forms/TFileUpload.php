<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Forms;

use App;
use Nette;
use Nette\Http\FileUpload;
use Nette\Image;
use Nette\Utils\Strings;
use Zenify;
use Zenify\Application\UI\Form;
use Zenify\Forms\Controls\UploadControl;
use Zenify\Utils\Filer;


trait TFileUpload
{
	/** @inject @var App\Files */
	public $files;

	/** @var string */
	private $imagesDir = '/images/uploaded/';

	/** @var string */
	private $storageDir = '/files/uploaded/';


	/**
	 * @param  string[]
	 * @param  Form
	 */
	public function processFileUploads(&$values, Form $form)
	{
		foreach ($values as $key => $value) {
			if ($form[$key] instanceof UploadControl) {
				$files = [];
				if (is_array($value)) {
					foreach ($value as $file) {
						$files[] = $this->saveFile($file, $form[$key], $key, TRUE);
					}

				} else {
					$files = $this->saveFile($value, $form[$key], $key);
				}

				$values[$key] = $files;
			}
		}
	}


	/**
	 * @param  FileUpload
	 * @param  UploadControl
	 * @param  string
	 * @param  bool
	 */
	private function saveFile(FileUpload $fileUpload, UploadControl $uploadControl, $key, $multiple = FALSE)
	{
		if ($fileUpload->isOk()) {
			$file = new App\File;

			$file->nameOrigin = $fileUpload->name;
			$file->name = $this->getUniqueName($fileUpload->name);

			if ($fileUpload->isImage()) {
				$file->path = $this->imagesDir;
				$image = $fileUpload->toImage();

				// default resize
				$resize = $uploadControl->getResize();
				if ($resize == NULL) {
					$resize[] = [
						'width' => 1024,
						'height' => 800
					];
				}

				if ($resize) {
					$i = 1;
					foreach ($resize as $dimensions) {
						if ($dimensions['width'] && $dimensions['height']) {
							$options = Image::SHRINK_ONLY | Image::EXACT;

						} else {
							$options = Image::SHRINK_ONLY;
						}

						$image->resize($dimensions['width'], $dimensions['height'], $options);

						try {
							$image->save($this->getSavePath($file));

						} catch (\Exception $e) {
							$fileUpload->move($this->getSavePath($file));
						}

						/*
						if ($i > 1) {
							// custom size folders (w60h100, w100, h20)
						}
						*/

						$i++;
					}
				}

			} else {
				$file->path = $this->storageDir;
				$fileUpload->move($this->getSavePath($file));
			}

			$this->files->save($file);
			return $file;
		}
	}


	/**
	 * @param  string
	 * @return  string
	 */
	private function getUniqueName($name)
	{
		$extensions = Filer::extension($name);
		$filename = Strings::webalize(Filer::filename($name));

		$file = $filename . '.' . $extensions;

		$i = 1;
		while (file_exists($this->imagesDir . $file)) {
			$file = $filename . '_' . $i++ . '.' . $extension;
		}

		return $file;
	}


	/**
	 * @param  App\File
	 * @return string
	 */
	private function getSavePath(App\File $file)
	{
		$path = $this->paramService->wwwDir . $file->path. $file->name;
		$dir = dirname($path);
		if (file_exists($dir) == FALSE) {
			throw new \Exception("Dir '$dir' doesn't exists.");
		}

		return $path;
	}

}
