<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 12/12/2015
 * Time: 13:51
 */

require_once 'vendor/autoload.php';

use App\Orm\Orm;

define('LOG_ACTIVE', true);

function do_tabs($tabs)
{
    $ret = "";
    for ($i= 0; $i < $tabs; $i++) {
        $ret .= "\t";
    }
    return $ret;
}

$host = $argv[1];
$user = $argv[2];
$password = $argv[3];
$db = $argv[4];
$tableName = $argv[5];
$className = $argv[6];

Orm::init($host, $db, $user, $password);

// Do some magic here
$fields = Orm::getTableColumns($tableName);
$tabs = 1;
$code = "<?php\n\n";
$code .= "namespace App\\Entity;\n\n";
$code .= "use App\\Orm\\QueryManager;\n\n";
$code .= "class $className extends QueryManager\n{\n";
$code .= do_tabs($tabs) . "/** PROPERTIES */\n";
$code .= do_tabs($tabs) . 'private $tableName = "'.$tableName.'";'."\n";
foreach ($fields as $field)
{
    if (strtolower($field) == 'id')
        continue;

    $code .= do_tabs($tabs) . 'private $'.$field.";\n";
}
$code .= do_tabs($tabs) . 'private $isUnique = "' . Orm::getUniqueColumnName($tableName) . '";'."\n";

$code .= "\n".do_tabs($tabs) . "/** SETTER */\n";
foreach ($fields as $field)
{
    if (strtolower($field) == 'id')
        continue;

    $code .= do_tabs($tabs) . 'public function set'.ucfirst($field).'($'.$field.")\n";
    $code .= do_tabs($tabs) . "{\n";
    $code .= do_tabs($tabs+1) . '$this->'.$field.' = $'.$field.";\n";
    $code .= do_tabs($tabs) . "}\n\n";
}

$code .= do_tabs($tabs) . "/** GETTER */\n";
foreach ($fields as $field) {

    if (strtolower($field) == 'id')
        continue;

    $code .= do_tabs($tabs) . 'public function get'.ucfirst($field)."()\n";
    $code .= do_tabs($tabs) . "{\n";
    $code .= do_tabs($tabs+1) . 'return $this->'.$field.";\n";
    $code .= do_tabs($tabs) . "}\n\n";
}

$code .= do_tabs($tabs) . "/** ORM NEEDLE */\n";
$code .= do_tabs($tabs) . "public function getTableName()\n";
$code .= do_tabs($tabs) . "{\n";
$code .= do_tabs($tabs+1) . 'return $this->tableName'.";\n";
$code .= do_tabs($tabs) . "}\n\n";

$code .= do_tabs($tabs) . "public function getIsUnique()\n";
$code .= do_tabs($tabs) . "{\n";
$code .= do_tabs($tabs+1) . 'return $this->isUnique'.";\n";
$code .= do_tabs($tabs) . "}\n\n";

$code .= do_tabs($tabs) . "/** SAVE */\n";
$code .= do_tabs($tabs) . "public function save()\n";
$code .= do_tabs($tabs) . "{\n";
$code .= do_tabs($tabs+1) . '$this->persist($this)'.";\n";
$code .= do_tabs($tabs) . "}\n\n";

$code .= do_tabs($tabs) . "/** METHODS */\n";
foreach ($fields as $field) {

    if (strtolower($field) == 'password')
        continue;

    $code .= do_tabs($tabs) . 'public function getBy'.ucfirst($field).'($'.$field.')'."\n";
    $code .= do_tabs($tabs) . "{\n";
    $code .= do_tabs($tabs+1) . 'return $this->select($this->tableName)->where(\''.$field.' = \\\'\'.$'.$field.'.\'\\\'\')->execute()'.";\n";
    $code .= do_tabs($tabs) . "}\n\n";
}

$code .= "}\n";

file_put_contents("src/Entity/".$className.".php", $code);
