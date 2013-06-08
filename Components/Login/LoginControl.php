<?php

namespace Components;

use Schmutzka\Application\UI\Form;
use Schmutzka\Application\UI\Control;

class LoginControl extends Control
{
	/** @var string */
	public $loginColumn = "email";

	/** @var array */
	public $onLoginSuccess = array();

	/** @var bool */
	public $permalogin = FALSE;

	/** @var array */
	public $onLoginError = array();

	/** @inject @var Schmutzka\Security\User */
	public $user;

	/** @inject @var Schmutzka\Config\ParamService */
	public $paramService;

	/** @inject @var Nette\Http\Session */
	public $session;


	protected function createComponentForm()
	{
		$form = new Form;
		if ($this->loginColumn == "login") {
			$form->addText("login", $this->paramService->form->login->label)
				->addRule(Form::FILLED, $this->paramService->form->login->ruleFilled)
				->addRule(~Form::EMAIL, $this->paramService->form->login->ruleFormat);

		} elseif ($this->loginColumn == "email") {
			$form->addText("login", $this->paramService->form->email->label)
				->addRule(Form::FILLED, $this->paramService->form->email->ruleFilled)
				->addRule(Form::EMAIL, $this->paramService->form->email->ruleFormat);
		}

		$form->addPassword("password", $this->paramService->form->password->label)
			->addRule(Form::FILLED, $this->paramService->form->password->ruleFilled);

		if ($this->permalogin) {
			$form->addCheckbox("permalogin", $this->paramService->form->permalogin->label)
				->setDefaultValue(TRUE);
		}

		$form->addSubmit("send", $this->paramService->form->send->login)
			->setAttribute("class", "btn btn-primary");

		return $form;
	}


	public function processForm($form)
	{
		try {
			$values = $form->values;

			if ($this->permalogin && $values["permalogin"]) {
				$this->user->setExpiration("+ 14 days", FALSE);

			} else {
				$this->user->setExpiration("+ 6 hours", TRUE);
			}

			$this->user->login($values["login"], $values["password"]);

			if ($this->onLoginSuccess) {
				$this->onLoginSuccess($this->user);
			}

			if ($this->paramService->flashes->onLogin) {
				$this->presenter->flashMessage($this->paramService->flashes->onLogin, "success");
			}

			$sectionKey = substr(sha1($this->paramService->wwwDir), 6);
			$baseSession = $this->session->getSection("baseSession_" . $sectionKey);
			$this->presenter->restoreRequest($baseSession->backlink); // @todo refactor to absolute param - standart!
			$this->presenter->redirect("Homepage:default");

		} catch (\Nette\Security\AuthenticationException $e) {
			if ($this->onLoginError) {
				$this->onLoginError($values);
			}

			$this->presenter->flashMessage($e->getMessage(), "error");
		}
	}


	public function renderAdmin()
	{
		parent::useTemplate("admin");
		$this->template->render();
	}

}