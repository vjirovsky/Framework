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
	/** @var [] */
	public $files;

	/** @var string */
	private $imagesDir = '/images/data/';

	/** @var string */
	private $storageDir = '/storage/files/';


	/**
	 * @param  array
	 * @param  Form
	 */
	public function processFileUploads(&$values, Form $form)
	{
		foreach ($values as $key => $value) {
			if ($form[$key] instanceof UploadControl) {
				if (is_array($value)) {
					foreach ($value as $file) {
						$this->processFileUpload($file, $form[$key], $key, TRUE);
					}

				} else {
					$this->processFileUpload($value, $form[$key], $key);
				}

				unset($values[$key]);
			}
		}
	}


	/**
	 * @param  FileUpload
	 * @param  UploadControl
	 * @param  string
	 * @param  bool
	 */
	private function processFileUpload(FileUpload $fileUpload, UploadControl $uploadControl, $key, $multiple = FALSE)
	{
		if ($fileUpload->isOk()) {
			$data = [
				'name_origin' => $fileUpload->getName(),
				'created' => new Nette\DateTime,
				'extension' => Filer::extension($fileUpload->getName()),
				'type' => $fileUpload->getContentType(),
				'size' => $fileUpload->getSize(),
			];


			if ($fileUpload->isImage()) {
				$resize = $uploadControl->getResize();

				$data['name'] = $this->getImageUniqueName($fileUpload->getName());
				$data['path'] = $this->imagesDir;

				$image = $fileUpload->toImage();

				// default resize
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
							$image->save($this->buildSavePath($data));

						} catch (\Exception $e) {
							$fileUpload->move($this->buildSavePath($data));
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
				$data['name'] = $this->getRandomName();
				$data['path'] = $this->storageDir;
				$fileUpload->move($this->buildSavePath($data));
			}

			$fileId = $this->fileModel->insert($data);

			if ($multiple) {
				$this->files[$key][] = $fileId;

			} else {
				$this->files[$key] = $fileId;
			}
		}
	}


	/**
	 * @return string
	 */
	private function getRandomName()
	{
		$rand = function() {
			return Strings::random(40, 'A-Za-z0-9_-');
		};

		$name = $rand();
		while ($this->fileModel->fetch(['name' => $name])) {
			$name = $rand();
		}

		return $name;
	}


	/**
	 * @param  string
	 * @return  string
	 */
	private function getImageUniqueName($name)
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
	 * @param  []
	 * @return string
	 */
	private function buildSavePath($data)
	{
		$path = $this->paramService->wwwDir . $data['path']. $data['name'];
		$dir = dirname($path);
		if (file_exists($dir) == FALSE) {
			mkdir($dir);
		}

		return $path;
	}

}
