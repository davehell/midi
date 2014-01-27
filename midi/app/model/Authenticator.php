<?php

use Nette\Security,
	Nette\Utils\Strings;


/**
 * Users authenticator.
 */
class Authenticator extends Nette\Object implements Security\IAuthenticator
{
	const
		TABLE_NAME = 'uzivatel',
		COLUMN_ID = 'id',
		COLUMN_NAME = 'login',
		COLUMN_PASSWORD = 'heslo',
    COLUMN_SALT = 'salt',
		PASSWORD_MAX_LENGTH = 1024;

	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * Performs an authentication.
	 * @param  array
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		$row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $username)->fetch();

		if (!$row) {
			throw new Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if ($row->heslo !== $this->generateHash($password, $row->heslo)) {
			throw new Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD]);
		return new Security\Identity($row->id, NULL, $arr);
	}


	/**
	 * Computes salted password hash.
	 * @return string
	 */
	public function generateHash($password, $salt)
	{
		return crypt($password, $salt);
	}


	/**
	 * Adds new user.
	 * @param  string
	 * @param  string
	 * @return void
	 */
	public function add($username, $password)
	{
    $salt = Strings::random(20);
		$this->database->table(self::TABLE_NAME)->insert(array(
			self::COLUMN_NAME => $username,
      self::COLUMN_SALT => $salt,
			self::COLUMN_PASSWORD => self::generateHash($password, $salt)
		));
	}
}
