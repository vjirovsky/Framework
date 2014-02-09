<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Utils;

use Nette;


class Name extends Nette\Object
{

	/**
	 * @param  Control
	 * @return string
	 */
	public static function daoFromControl($control)
	{
		$class = $control->reflection->name;
		$name = self::getAfterLastSlash($class);
		$name = lcfirst(substr($name, 0, -7)) . 's';

		return $name;
	}


	/**
	 * @param  Presenter
	 * @return string
	 */
	public static function daoFromPresenter($presenter)
	{
		$class = $presenter->reflection->name;
		$name = self::getAfterLastSlash($class);

		$name = lcfirst(substr($name, 0, -9));

		return $name .'s';
	}


	/**
	 * Modul/presenter/view
	 * @param Presenter
	 * @param string
	 * @return string
	 */
	public static function mpv($activePresenter, $part = NULL)
	{
		$module = NULL;
		$presenter = $activePresenter->name;
		if (strpos($presenter, ':')) {
			list($module, $presenter) = explode(':', $presenter, 2);
		}
		$view = lcfirst($activePresenter->view);
		$presenter = lcfirst($presenter);
		$module = lcfirst($module);

		if ($part == 'module') {
			return $module;

		} elseif ($part == 'presenter') {
			return $presenter;

		} elseif ($part == 'view') {
			return $view;
		}

		return array($module, $presenter, $view);
	}


	/**
	 * @param  string
	 * @return string
	 */
	public static function module($path)
	{
		return self::mpv($path, 'module');
	}


	/**
	 * @param  string
	 * @return string
	 */
	public static function presenter($path)
	{
		return self::mpv($path, 'presenter');
	}


	/**
	 * @param  string
	 * @return string
	 */
	public static function view($path)
	{
		return self::mpv($path, 'view');
	}


	/**
	 * @param string
	 * @return string
	 */
	public static function moduleFromNamespace($namespace)
	{
		$temp = explode('\\', $namespace);
		$module = substr($temp[0], 0, -6);
		$module = lcfirst($module);

		return $module;
	}


	/**
	 * @param  string
	 * @return string
	 */
	private static function getAfterLastSlash($string)
	{
		if ($slahPos = strrpos($string, '\\')) {
			return substr($string, $slahPos + 1);
		}

		return $string;
	}

}
