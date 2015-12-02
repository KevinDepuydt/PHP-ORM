<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 02/12/2015
 * Time: 14:28
 */

namespace App\Orm;


class ConnexionManager extends \PDO
{

    private static $connexion = null;

    public static function init($host, $db, $user, $password)
    {
        self::$connexion = new \PDO('mysql:host='.$host.';dbname='.$db.'', $user, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        self::$connexion->query("SET NAMES utf8");
    }

    public static function getConnexion()
    {
        return self::$connexion;
    }

}