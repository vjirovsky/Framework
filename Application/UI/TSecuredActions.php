<?php

namespace Schmutzka\Application\UI;

use Nette;
use Nette\Reflection;
use Nette\Security\User;
use Schmutzka;


trait TSecuredActions
{
	use Schmutzka\TAnnotations;


	public function beforeRender()
	{
		$this->authCheck();
		$this->unloggedCheck();
		parent::beforeRender();
	}


	/**
	 * Provides an authentication check on methods and classes marked with @secured[, @role] annotation
	 */
	protected function authCheck()
	{
		$annotation = 'secured';
		$authenticate = FALSE;

		if ($flag = $this->getViewAnnotation($this->view, $annotation)) {
			$authenticate = TRUE;

		} elseif ($flag = $this->getPresenterAnnotation($annotation)) {
			$authenticate = TRUE;
		}

		if ($authenticate) {
			if ( ! $this->user->isLoggedIn()) {
				$this->flashMessage('Pro přístup k této operaci se prosím přihlaste.', 'error');
				$backlink = $this->presenter->storeRequest();
				$this->redirect('Auth:signIn', ['backlink' => $backlink]);

			} elseif (isset($flag['role'])) {
				if (in_array($this->user->role, (array) $flag['role'][0]) == FALSE) {
					$this->flashMessage('Nemáte oprávnění pro přístup k této operaci.', 'error');
					$this->redirect('Homepage:default');
				}
			}
		}
	}


	/**
	 * Check if user is NOT logged.
	 * Redirect elsewhere.
	 */
	protected function unloggedCheck()
	{
		$annotation = 'unlogged';

		if ($this->user->loggedIn && ($this->getPresenterAnnotation($annotation) || $this->getViewAnnotation($this->view, $annotation))) {
			$this->redirect('Homepage:default');
		}
	}

}
