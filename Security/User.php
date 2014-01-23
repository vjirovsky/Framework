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

use Nette;


class User extends Nette\Security\User
{
	/** @inject @var Models\User */
	public $userModel;


	/**
	 * Identity property shortcut
	 * @param string
	 */
	public function &__get($name)
	{
		if ($this->getIdentity() && array_key_exists($name, $this->getIdentity()->data) && $name != 'roles') {
			$data = $this->getIdentity()->data;
			return $data[$name];
		}

		return Nette\ObjectMixin::get($this, $name);
	}


	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->getIdentity()->data;
	}


	/**
	 * @param []
	 */
	public function updateIdentity($values)
	{
		foreach ($this->identity->data as $key => $value) {
			if (array_key_exists($key, $values)) {
				$this->identity->{$key} = $values[$key];
			}
		}

		$this->userModel->update($values, $this->id);
	}


	/**
	 * @return  string
	 */
	public function getRole()
	{
		$roles = $this->roles;
		return array_pop($roles);
	}


	/**
	 * @param []
	 */
	public function autologin($cond)
	{
		$row = $this->userModel->fetch($cond);
		if ($row) {
			unset($row['password'], $row['salt']);
			$identity = new Nette\Security\Identity($row['id'], (isset($row['role']) ? $row['role'] : 'user'), $row);
			$this->login($identity);
		}
	}

}
