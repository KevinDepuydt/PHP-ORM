<?php

namespace App\Entity;

use App\Orm\QueryManager;

class User extends QueryManager
{
	/** PROPERTIES */
	private $tableName = "user";
	private $login;
	private $email;
	private $password;
	private $isUnique = "login";

	/** SETTER */
	public function setLogin($login)
	{
		$this->login = $login;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function setPassword($password)
	{
		$this->password = $password;
	}

	/** GETTER */
	public function getLogin()
	{
		return $this->login;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getPassword()
	{
		return $this->password;
	}

	/** ORM NEEDLE */
	public function getTableName()
	{
		return $this->tableName;
	}

	public function getIsUnique()
	{
		return $this->isUnique;
	}

	/** SAVE */
	public function save()
	{
		$this->persist($this);
	}

	/** METHODS */
	public function getById($id)
	{
		return $this->select($this->tableName)->where('id = \''.$id.'\'')->execute();
	}

	public function getByLogin($login)
	{
		return $this->select($this->tableName)->where('login = \''.$login.'\'')->execute();
	}

	public function getByEmail($email)
	{
		return $this->select($this->tableName)->where('email = \''.$email.'\'')->execute();
	}

}
