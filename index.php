<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 02/12/2015
 * Time: 14:23
 */

require_once 'vendor/autoload.php';

use App\Orm\Orm, App\Entity\User, App\Orm\QueryManager;

Orm::init('127.0.0.1', 'orm', 'root', '');

$user = new User();
$user->setLogin('simple');
$user->setEmail('simple@mail.fr');
$user->setPassword(sha1('simple'));
$user->save();
