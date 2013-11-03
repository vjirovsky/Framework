<?php

namespace Schmutzka\Application\UI;

use Nette;
use Nette\Reflection;
use Nette\Security\User;


trait TSecuredActions
{

	public function beforeRender()
	{
		$this->authCheck();
		parent::beforeRender();
	}


	/**
	 * Provides an authentication check on methods and classes marked with @secured[, @role] annotation
	 */
	protected function authCheck()
	{
		$annotation = 'secured';

		$actionMethod = $this->formatActionMethod($this->action);
		$signalMethod = $this->formatSignalMethod($this->signal);
		$renderMethod = $this->formatRenderMethod($this->view);

		if ($this->hasMethodAnnotation($actionMethod, $annotation)) {
			$authenticate = TRUE;
			$reflection = Reflection\Method::from($this, $actionMethod);
			$flag = $this->getAnnotation($reflection, $annotation);

		} elseif ($this->isSignalReceiver($this) && $this->hasMethodAnnotation($signalMethod, $annotation)) {
			$authenticate = TRUE;
			$reflection = Reflection\Method::from($this, $signalMethod);
			$flag = $this->getAnnotation($reflection, $annotation);

		} elseif ($this->hasMethodAnnotation($renderMethod, $annotation)) {
			$authenticate = TRUE;
			$reflection = Reflection\Method::from($this, $renderMethod);
			$flag = $this->getAnnotation($reflection, $annotation);

		} elseif ($this->hasAnnotation($annotation)) {
			$authenticate = TRUE;
			$flag = $this->getAnnotation($this->getReflection(), $annotation);

		} else {
			$authenticate = FALSE;
			$flag = NULL;
		}

		if (isset($reflection)) {
			$flags = $reflection->getAnnotations();
		}

		if ($authenticate) {
			if ( ! $this->user->isLoggedIn()) {
				$this->flashMessage('Sign in first', 'warning');
				// $this->flashMessage('Pro přístup k této operaci se prosím přihlaste.', 'warning');
				$backlink = $this->presenter->storeRequest();
				$this->redirect('Auth:signIn', ['backlink' => $backlink]);

			} elseif (isset($flags['role'])) {
				if (in_array($this->user->role, (array) $flags['role'][0]) == FALSE) {
					$this->flashMessage('Forbidden access.', 'error');
					// $this->flashMessage('Nemáte oprávnění pro přístup k této operaci.', 'error');
					$this->redirect('Homepage:default');
				}
			}
		}
	}


	/**
	 * Checks if class has a given annotation
	 * @param string $annotation
	 * @return bool
	 */
	protected function hasAnnotation($annotation)
	{
		return $this->getReflection()->hasAnnotation($annotation);
	}


	/**
	 * Checks if given method has a given annotation
	 * @param string $method
	 * @param string $annotation
	 * @return bool
	 */
	protected function hasMethodAnnotation($method, $annotation)
	{
		if ( ! $this->getReflection()->hasMethod($method)) return FALSE;

		$rm = Reflection\Method::from($this->getReflection()->getName(), $method);
		return $rm->hasAnnotation($annotation);
	}


	/**
	 * Get all anotations of given name
	 * @param object $reflection
	 * @param string $name
	 * @return mixed
	 */
	protected function getAnnotation($reflection, $name)
	{
		$res = $reflection->getAnnotations();
		if (isset($res[$name]))	{
			if (sizeof($res[$name]) > 1) {
				return $res[$name];

			} else {
				return end($res[$name]);
			}

		} else {
			return NULL;
		}
	}

}
