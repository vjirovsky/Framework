<?php

namespace Schmutzka\Application\UI;

use Schmutzka;


trait TSecuredActions
{
	use Schmutzka\TAnnotations;


	public function checkSecuredAnnotations()
	{
		$this->authCheck();
		$this->unloggedCheck();
	}


	/**
	 * Provides an authentication check on methods and classes marked with @secured or @role annotation
	 */
	public function authCheck()
	{
		$annotation = 'secured';
		$authenticate = FALSE;

		if ($flag = $this->getViewAnnotation($this->view, $annotation)) {
			$authenticate = TRUE;

		} elseif ($this->getPresenterAnnotation($annotation)) {
			if ($this->getPresenterAnnotation('role')) {
				$flag['role'][0] = $this->getPresenterAnnotation('role');
			}
			$authenticate = TRUE;
		}

		if ($authenticate) {
			if ( ! $this->user->isLoggedIn()) {
				$this->flashMessage('Pro přístup k této operaci se prosím přihlaste.', 'danger');
				$backlink = $this->presenter->storeRequest();
				$this->redirect('Auth:signIn', ['backlink' => $backlink]);

			} elseif (isset($flag['role']) && is_array($flag['role'])) {
				if (in_array($this->user->role, (array) $flag['role'][0]) == FALSE) {
					$this->flashMessage('Na tuto stránku nemáte přístup.', 'danger');
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
