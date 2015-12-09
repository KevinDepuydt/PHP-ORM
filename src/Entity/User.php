<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 09/12/2015
 * Time: 12:03
 */

namespace App\Entity;


class User
{
    /** PROPERTIES */
    private $tableName = 'user';
    private $login;
    private $mail;
    private $password;

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

    public function getEmail()
    {
        return $this->mail;
    }

    public function getPassword()
    {
        return $this->password;
    }

    /** PERSIST */
    public function persist()
    {

    }

}