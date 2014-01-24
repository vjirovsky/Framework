<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Security;


trait TCheckRequirements
{
	/** @var Nette\Security\Permission */
	private $acl;


	public function injectAcl(Nette\Security\IAuthorizator $acl = NULL)
	{
		$this->acl = $acl;
	}


	/**
	 * @param  mixed
	 */
	public function checkRequirements($element)
	{
		if ($element instanceof Nette\Application\UI\PresenterComponentReflection) {
			$annotations = $element->getAnnotations();
			$this->processAnnotations($annotations);
		}

		$this->processMethodAnnotations($this->formatActionMethod($this->view));
		$this->processMethodAnnotations($this->formatRenderMethod($this->view));

		if ($this->user->loggedIn && $this->acl) {
			$this->requirePrivilege($this->name, $this->action);
		}
	}


	/**
	 * @param  string
	 */
	private function processMethodAnnotations($method)
	{
		if ( ! method_exists($this, $method)) {
			return;
		}

		$reflection = $this->getReflection()->getMethod($method);
		$annotations = $reflection->getAnnotations();

		$this->processAnnotations($annotations);
	}


	/**
	 * @param  []
	 */
	private function processAnnotations($annotations)
	{
		if (isset($annotations['secured'])) {
			$this->requireLogin();
		}

		if (isset($annotations['role'])) {
			$this->requireRole($annotations['role']);
		}

		if (isset($annotations['unlogged'])) {
			$this->requireUnlogged();
		}
	}


	/**
	 * @param  []
	 */
	private function requireRole($roles)
	{
		if ( ! isset($roles[$this->user->role])) {
			$this->requireLogin();
		}
	}


	/**
	 * @param  string
	 * @param  string
	 */
	private function requirePrivilege($resource, $action = NULL)
	{
		if ( ! $this->user->isAllowed($resource, $action)) {
			$this->requireLogin();
		}
	}


	private function requireLogin()
	{
		if ( ! $this->user->isLoggedIn()) {
			$storedRequest = $this->presenter->storeRequest();
			$this->flashMessage('Pro přístup do této sekce se musíte přihlásit.', 'info');

			if ($this->module != 'front') {
				$this->redirect(':Admin:Homepage:default', ['backlink' => $storedRequest]);

			} else {
				$this->redirect(':Front:Auth:signIn', ['backlink' => $storedRequest]);
			}
		}
	}


	private function requireUnlogged()
	{
		if ($this->user->loggedIn) {
			$this->redirect('Homepage:default');
		}
	}

}
