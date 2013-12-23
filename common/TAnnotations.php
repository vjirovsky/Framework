<?php

namespace Schmutzka;


trait TAnnotations
{
	/** @var Nette\Reflection\ClassType */
	private $reflection;


	/**
	 * @param  string
	 * @return  NULL|array
	 */
	protected function getPresenterAnnotation($annotation)
	{
		$ref = $this->getRef();

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
		$ref = $this->getRef();

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


	/**
	 * @return  Nette\Reflection\ClassType
	 */
	protected function getRef()
	{
		if ($this->reflection == NULL) {
			$this->reflection = $this->getReflection();
		}

		return $this->reflection;
	}

}
