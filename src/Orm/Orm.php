<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 02/12/2015
 * Time: 14:28
 */

namespace App\Orm;

use App\Exceptions\ConnexionException;

class Orm extends \PDO
{
    private static $connexion = null;

    public static function init($host, $db, $user, $password)
    {
        try {
            self::$connexion = new \PDO('mysql:host='.$host.';dbname='.$db.'', $user, $password, [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]);
            self::$connexion->query("SET NAMES utf8");

        } catch (ConnexionException $e) {
            throw new ConnexionException('Impossible de se connecter a la base de données '.$db.' : '.$e->getMessage());
        }
    }

    public static function getConnexion()
    {
        return self::$connexion;
    }

}