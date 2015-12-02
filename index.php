<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 02/12/2015
 * Time: 14:23
 */

require_once 'vendor/autoload.php';

use App\Orm\ConnexionManager;

ConnexionManager::init('127.0.0.1', 'orm', 'root', '');

var_dump(ConnexionManager::getConnexion());
