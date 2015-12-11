<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 09/12/2015
 * Time: 12:03
 */

namespace App\Entity;

use App\Exceptions\QueryManagerException;
use App\Orm\QueryManager;

class User extends QueryManager
{
    /** PROPERTIES */
    private $tableName = 'user';
    private $login;
    private $mail;
    private $password;
    private $isuniq = 'login';

    public function __construct()
    {
    }

    /** SETTER */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setEmail($mail)
    {
        $this->mail = $mail;
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

    public function getMail()
    {
        return $this->mail;
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

    public function getIsuniq()
    {
        return $this->isuniq;
    }

    /** SAVE */
    public function save()
    {
        $this->persist($this);
    }

    /** METHODS */
    public function getById($id)
    {
        return $this->select($this->tableName)->where('id = '.$id)->execute();
    }

    public function getByLogin($id)
    {
        return $this->select($this->tableName)->where('login = \''.$id.'\'')->execute();
    }

    public function getByMail($mail)
    {
        return $this->select($this->tableName)->where('mail = \''.$mail.'\'')->execute();
    }

}