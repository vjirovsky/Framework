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

use App;
use Nette;
use Nette\Security\AuthenticationException as AE;
use Nette\Utils\Strings;
use Models;
use Zenify;


class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
{
	/** @var Zenify\ParamService */
	private $paramService;

	/** @var App\Users */
	private $users;

	/** @var Nette\Localization\ITranslator */
	private $translator;


	public function __construct(App\Users $users, Zenify\ParamService $paramService)
	{
		$this->users = $users;
		$this->paramService = $paramService;
	}


	/**
	 * @return Nette\Security\Identity
	 */
	public function authenticate(array $credentials)
	{
		list($email, $password) = $credentials;

		$user = $this->users->findOneBy(['email' => $email]);

		if ( ! $user) {
			throw new AE('nonExistingAccount', self::IDENTITY_NOT_FOUND);
		}

		if (self::verifyPassword($password, $user->salt, $user->password) == FALSE) {
			throw new AE('wrongPassword', self::INVALID_CREDENTIAL);
		}

		return new Nette\Security\Identity($user->id, $user->role, $user->identityData);
	}


	/**
	 * @param array
	 * @return App\User
	 */
	public function add($values)
	{
		if ( ! isset($values['salt'])) {
			$values['salt'] = self::makeSalt();
		}
		$values['password'] = self::hashPassword($values['password'], $values['salt']);

		if ($this->users->findOneBy(['email' => $values['email']])) {
			throw new \Exception('Tento email je již zaregistrován. Použijte prosím jiný.');
		}

		$user = new App\User;
		foreach ($values as $key => $value) {
			$user->$key = $value;
		}

		return $this->users->save($values);
	}


	/**
	 * @param  string
	 * @param  string
	 * @return string
	 */
	public static function hashPassword($password, $salt)
	{
		return crypt($password, $salt);
	}


	/**
	 * @return  string
	 */
	public static function makeSalt()
	{
		return implode('$', [
			'algo' => '$2y',
			'cost' => '07',
			'salt' => Strings::random(22),
		]);
	}


	/**
	 * @return bool
	 */
	public static function verifyPassword($password, $salt, $hash)
	{
		return self::hashPassword($password, $salt) === $hash;
	}


	/**
	 * @param []
	 * @param string
	 */
	public function updatePassword($cond, $password)
	{
		$user = $this->users->findOneBy($cond);
		$user->salt = $salt = self::makeSalt();
		$user->password = self::hashPassword($password, $salt);
		$this->users->save($user);
	}

}
