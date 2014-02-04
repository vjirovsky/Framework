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
use Nette\Security\AuthenticationException as AE;
use Nette\Utils\Strings;
use Models;
use Zenify;


class UserManager extends Nette\Object implements Nette\Security\IAuthenticator
{
	/** @var Zenify\ParamService */
	private $paramService;

	/** @var Models\User */
	private $userModel;

	/** @var Nette\Localization\ITranslator */
	private $translator;


	public function __construct(Models\User $userModel, Zenify\ParamService $paramService)
	{
		$this->userModel = $userModel;
		$this->paramService = $paramService;
	}


	/**
	 * @return Nette\Security\Identity
	 */
	public function authenticate(array $credentials)
	{
		list($email, $password) = $credentials;

		$row = $this->userModel->fetch(['email' => $email]);

		if ( ! $row) {
			throw new AE('nonExistingAccount', self::IDENTITY_NOT_FOUND);
		}

		if (self::verifyPassword($password, $row['salt'], $row['password']) == FALSE) {
			throw new AE('wrongPassword', self::INVALID_CREDENTIAL);
		}

		unset($row['password'], $row['salt']);
		return new Nette\Security\Identity($row['id'], $row['role'], $row->toArray());
	}


	/**
	 * @param array
	 * @return NotORM_Row
	 */
	public function add($values)
	{
		if ( ! isset($values['salt'])) {
			$values['salt'] = self::makeSalt();
		}
		$values['password'] = self::hashPassword($values['password'], $values['salt']);

		if ($this->userModel->count(['email' => $values['email']])) {
			throw new \Exception('Tento email je již zaregistrován. Použijte prosím jiný.');
		}

		return $this->userModel->insert($values);
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
		$salt = self::makeSalt();
		$this->userModel->update([
			'salt' => $salt,
			'password' => self::hashPassword($password, $salt)
		], $cond);
	}

}
