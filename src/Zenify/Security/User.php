<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Security;

use Nette;


class User extends Nette\Security\User
{
	/** @inject @var App\Users */
	public $users;


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
	 * @return  string
	 */
	public function getRole()
	{
		$roles = $this->roles;
		return array_pop($roles);
	}


	/**
	 * @param string[]
	 */
	public function autologin($cond)
	{
		$user = $this->users->findOneBy($cond);

		if ($user) {
			$identity = new Nette\Security\Identity($user->id, (isset($user->role) ? $user->role : 'user'), $user->identityData);
			$this->login($identity);
		}
	}

}
