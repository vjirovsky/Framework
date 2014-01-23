<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka;


trait TAnnotations
{

	/**
	 * @param  string
	 * @return  NULL|array
	 */
	protected function getPresenterAnnotation($annotation)
	{
		$ref = $this->getReflection();

		if ($ref->hasAnnotation($annotation)) {
			return $ref->getAnnotation($annotation);
		}

		return NULL;
	}


	/**
	 * @param  string
	 * @param  string
	 * @return bool|string|array
	 */
	protected function getViewAnnotation($view, $annotation)
	{
		if ($flag = $this->getMethodAnnotation('action' . ucfirst($view), $annotation)) {
			return $flag;

		} elseif ($flag = $this->getMethodAnnotation('render' . ucfirst($view), $annotation)) {
			return $flag;
		}

		return FALSE;
	}


	/**
	 * @param  string
	 * @param  string
	 * @return bool|string|array
	 */
	protected function getMethodAnnotation($method, $annotation)
	{
		$ref = $this->getReflection();

		if ($ref->hasMethod($method)) {
			$refMethod = $ref->getMethod($method);
			if ($refMethod->hasAnnotation($annotation)) {
				if ($flag = $refMethod->getAnnotation($annotation)) {
					return $flag;
				}

				return TRUE;
			}
		}

		return FALSE;
	}

}
