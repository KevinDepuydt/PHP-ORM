<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 02/12/2015
 * Time: 15:02
 */

namespace App\Orm;

use App\Exceptions\TableManagerException;
use App\Interfaces\TableManagerInterface;

class TableManager implements TableManagerInterface
{

    private $tableName = '';
    private $query = '';
    private $fieldsQuery = [];
    private $db;

    public function __construct($name)
    {
        $this->db = ConnexionManager::getConnexion();
        $this->setName($name);
    }

    public function setName($name)
    {
        $this->tableName = $name;
    }

    public function getName()
    {
        return $this->tableName;
    }

    public function create()
    {
        $this->query = 'CREATE TABLE IF NOT EXISTS '.$this->tableName;

        array_unshift($this->fieldsQuery, 'id INT NOT NULL AUTO_INCREMENT,PRIMARY KEY(id)');

        $this->query .= '(' . $this->buildFields($this->fieldsQuery) . ')';

        return ConnexionManager::getConnexion()->prepare($this->query)->execute();
    }

    public function remove()
    {
        $this->query = 'DROP TABLE '.$this->tableName;

        return ConnexionManager::getConnexion()->prepare($this->query)->execute();
    }

    public function edit()
    {
        $this->query = 'ALTER TABLE '.$this->tableName . ' ' . $this->buildFields($this->fieldsQuery) . ';';

    }

    public function delete()
    {
        $this->query = '';
    }

    public function addField($name, $type, $size = null)
    {

        if (in_array($name, ['id', 'ID', 'Id', 'iD']))
            throw new TableManagerException('Le champ ID est créé automatiquement');

        switch ($type) {
            case 'varchar':
                $this->fieldsQuery[] = $name . ' VARCHAR(' . (($size != null && $size <= 255) ? $size : 255) . ') NOT NULL';
                break;
            case 'int':
                $this->fieldsQuery[] = $name . ' INT' . (($size != null && $size > 0) ? '('.$size.') NOT NULL' : 'NOT NULL');
                break;
            case 'date':
                $this->fieldsQuery[] = $name . ' DATE';
                break;
            default:
                throw new TableManagerException('Le type du champ '.$name.' est invalide');
                break;
        }

        return $this;
    }

    public function removeField($name)
    {
        if (in_array($name, ['id', 'ID', 'Id', 'iD']))
            throw new TableManagerException('Le champ ID n\'a pas besoin d\'être défini, il est créé automatiquement');

        $this->fieldsQuery[] = ''; //@TODO finir alter table
    }

    private function buildFields($fields)
    {
        return implode(',', $fields);
    }
}