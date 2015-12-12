<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 02/12/2015
 * Time: 14:28
 */

namespace App\Orm;

use App\Exceptions\ConnexionException;
use App\Exceptions\QueryManagerException;

class Orm extends \PDO
{
    private static $connexion = null;
    private static $logSqlPath = 'log/access.log';
    private static $logErrorPath = 'log/error.log';

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

    public static function logSql($sql)
    {
        if (!LOG_ACTIVE)
            return true;

        if (file_exists(self::$logSqlPath))
            $file = file_get_contents(self::$logSqlPath);
        else
            $file = '';

        $file .= "[".date("d-m-Y H:i:s")."] La requete SQL >> ".$sql." << a été exécutée\n";

        file_put_contents(self::$logSqlPath, $file);
    }

    public static function logError($sql,\Exception $e)
    {
        if (!LOG_ACTIVE)
            return true;

        if (file_exists(self::$logErrorPath))
            $file = file_get_contents(self::$logErrorPath);
        else
            $file = '';

        $file .= "[".date("d-m-Y H:i:s")."] La requete SQL >> ".$sql." << a échouée, erreur : " . $e->getMessage() . "\n";

        file_put_contents(self::$logErrorPath, $file);

    }
}