<?php

namespace Schmutzka;

use Nette;
use Nette\Image;


/**
 * @method setTargetDir(string)
 * @method setName(string)
 * @method addResize
 * @method setResizes
 * @method getResizes
 */
class File extends Nette\Object
{
	/** @var array */
	protected $resizes;

	/** @var Nette\Http\FileUpload */
	private $file;

	/** @var string */
	private $name;

	/** @var string */
	private $targetDir;


	public function __construct(Nette\Http\FileUpload $file)
	{
		$this->file = $file;
	}


	public function save()
	{
		$this->checkDirExistance();

		if ($this->resizes && $this->file->isImage()) {
			$image = $this->file->toImage();
			foreach ($this->resizes as $value) {
				$image->resize($value[0], $value[1], Image::SHRINK_ONLY | Image::EXACT);
				$image->save($this->getTargetFilePath(). '.jpg');
			}

		} else {
			$this->file->save($this->getTargetFilePath());
		}
	}


	/**
	 * @return string
	 */
	private function getTargetFilePath()
	{
		return $this->targetDir . $this->name;
	}


	/**
	 * Check if dir exist. Create if not.
	 */
	private function checkDirExistance()
	{
		if (file_exists($this->targetDir) == FALSE) {
			mkdir($this->targetDir);
		}
	}

}
