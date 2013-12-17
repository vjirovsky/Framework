<?php

namespace Schmutzka\Forms;

use Nette;
use Nette\Image;
use Nette\Utils\Strings;
use Schmutzka\Utils\Filer;


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
	 */
	public function processFileUploads(&$values)
	{
		foreach ($values as $key => $value) {
			if ($value instanceof Nette\Http\FileUpload) {
				if ($value->isOk()) {

					$data = [
						'name_origin' => $value->getName(),
						'created' => new Nette\DateTime,
						'extension' => Filer::extension($value->getName()),
						'type' => $value->getContentType(),
						'size' => $value->getSize(),
					];


					if ($value->isImage()) {
						$control = $this->form[$key];
						$resize = $control->getResize();

						$data['name'] = $this->getImageUniqueName($value->getName());
						$data['path'] = $this->imagesDir;

						$image = $value->toImage();

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
								$image->save($this->paramService->wwwDir . $data['path'] . $data['name']);

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

						$value->move($this->paramService->wwwDir . $data['path']. $data['name']);
					}

					$fileId = $this->fileModel->insert($data);
					$this->files[$key] = $fileId;
				}

				unset($values[$key]);
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

}
