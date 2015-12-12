<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 02/12/2015
 * Time: 14:23
 */

require_once 'vendor/autoload.php';

// enable/disable logs
define('LOG_ACTIVE', false);

use App\Orm\Orm,
    App\Entity\User,
    App\Orm\QueryManager;

Orm::init('127.0.0.1', 'orm', 'root', '');

$user = new User();

for ($i = 1; $i < 11; $i++)
{
    $user->setLogin('simple'.$i);
    $user->setEmail('simple'.$i.'@mail.fr');
    $user->setPassword(sha1('simple'.$i));
    $user->save();
}

$q = new QueryManager();
// var_dump($q->select('user u')->join('LEFT JOIN test t ON t.user_id = u.id')->where('u.id = 3')->execute()); // select // join // where
