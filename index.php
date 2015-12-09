<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 02/12/2015
 * Time: 14:23
 */

require_once 'vendor/autoload.php';

use App\Orm\Orm, App\Entity\User;

Orm::init('127.0.0.1', 'orm', 'root', '');

var_dump(Orm::getConnexion());

$user = new User();
$user->setLogin('login');
$user->setEmail('mail@mail.fr');
$user->setPassword(sha1('password'));
$user->persist();

var_dump($user);
