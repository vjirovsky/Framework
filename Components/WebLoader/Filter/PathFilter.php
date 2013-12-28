<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Components\WebLoader\Filter;

use Nette;
use WebLoader;


class PathFilter extends Nette\Object
{

	/**
	 * @param string
	 * @param WebLoader\Compiler
	 * @return string
	 */
	public function __invoke($code, WebLoader\Compiler $loader)
	{
		$code = strtr($code, array(
			'url(../' => 'url(../../',
			"url('../" => "url('../../",
			'url("../' => 'url("../../',
			"url('chosen-sprite" => "url('../../images/chosen/chosen-sprite",
			"url('../fonts/fontawesome" => "url('../../modules/font-awesome/fonts/fontawesome",
			'url("../img' => 'url("../../images/cms',
			"url('../img" => "url('../../images/cms",
			'url(../img' => 'url(../../images/cms',
			"url('../" => "url('../../",
			'url("chosen' => 'url("../../images/cms/chosen/chosen'
		));

		return $code;
	}

}
