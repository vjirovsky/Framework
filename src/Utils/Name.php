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
	 * @param  Reflection
	 * @return string
	 */
	public static function modelFromControlReflection($reflection)
	{
		$fullClassName = $reflection->getName();
		$model = substr($fullClassName, strrpos($fullClassName, '\\') + 1);
		return lcfirst(substr($model, 0, -7)) . 'Model';
	}


	/**
	 * @param string
	 * @return string
	 * @example Models\Pages => pages, Models\ArticleTag => article_tag
	 */
	public static function tableFromClass($class)
	{
		$table = substr($class, strrpos($class, '\\') + 1);
		$table = lcfirst($table);
		return self::upperToUnderscoreLower($table);
	}


	/**
	 * @param string
	 * @return string
	 * @example CustomModule => custom-module
	 */
	public static function upperToDashedLower($string)
	{
		return strtr($string, self::getReplaceAlphabetBy('-'));
	}


	/**
	 * @param string
	 * @return string
	 * @example customTable => custom_table
	 */
	public static function upperToUnderscoreLower($string)
	{
		return strtr($string, self::getReplaceAlphabetBy('_'));
	}


	/**
	 * @param Nette\Application\UI\PresenterComponentReflection
	 * @param string
	 * @return string|NULL
	 */
	public static function templateFromReflection(Nette\Application\UI\PresenterComponentReflection $reflection, $name = NULL)
	{
		$file = dirname($reflection->getFileName()) . '/' . $reflection->getShortName() . ucfirst($name) . '.latte';
		if (file_exists($file)) {
			return $file;
		}

		return NULL;
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
	 * Modul from namespace
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
	 * @return array
	 */
	private static function getReplaceAlphabetBy($char)
	{
		$replace = array();
		foreach (range('A', 'Z') as $letter) {
			$replace[$letter] = $char . strtolower($letter);
		}

		return $replace;
	}

}
