<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 09/12/2015
 * Time: 12:21
 */

namespace App\Orm;

use App\Exceptions\QueryManagerException;
use App\Interfaces\QueryManagerInterface;

class QueryManager implements QueryManagerInterface
{
    private $query;
    private $baseQuery;
    private $where = [];
    private $join = [];
    private $orderBy = [];
    private $fields = [];
    private $type;

    public function select($tablename, $column = '*')
    {
        if (is_array($column))
            $column = implode(',', $column);

        $this->type = 'select';
        $this->baseQuery = 'SELECT ' . $column . ' FROM ' . $tablename;

        return $this;
    }

    public function insert($tablename)
    {
        $this->type = 'insert';
        $this->baseQuery = 'INSERT INTO ' . $tablename;

        return $this;
    }

    public function addField($name, $value)
    {
        if (empty($name) || empty($value))
            throw new QueryManagerException('Field name or value can\'t be empty');

        $this->fields[$name] = $value;

        return $this;
    }

    public function delete($tablename)
    {
        $this->type = 'delete';
        $this->baseQuery = 'DELETE FROM ' . $tablename;

        return $this;
    }

    public function update($tablename)
    {
        $this->type = 'update';
        $this->baseQuery = 'UPDATE ' . $tablename . ' SET ';

        return $this;
    }

    /** HELPERS */
    public function count($tablename)
    {
        $query = 'SELECT COUNT(id) FROM '.$tablename;
        $result = null;
        try {
            $result = Orm::getConnexion()->prepare($query);
            $result->execute();
            $result = $result->fetch(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            Orm::logError($query, $e);
        }

        Orm::logSql($query);

        return $result;
    }

    public function exist($tablename, $field, $value)
    {
        $query = 'SELECT ' . $field . ' FROM '.$tablename. ' WHERE ' . $field . ' = \'' . $value . '\'';
        $result = null;
        try {
            $result = Orm::getConnexion()->prepare($query);
            $result->execute();
            $result = ($result->fetch(\PDO::FETCH_COLUMN)) ? true : false;
        } catch (\Exception $e) {
            Orm::logError($query, $e);
        }

        Orm::logSql($query);

        return $result;
    }

    public function getTableColumns($tablename)
    {
        $query = 'DESCRIBE '.$tablename;
        $result = null;
        try {
            $result = Orm::getConnexion()->prepare($query);
            $result->execute();
            $result = $result->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            Orm::logError($query, $e);
        }

        Orm::logSql($query);

        return $result;
    }

    /** QUERIES CONDITIONS */
    public function join($join)
    {
        $this->join = $join;
        return $this;
    }

    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function where($where)
    {
        $this->where = $where;
        return $this;
    }

    /** EXECUTION */
    private function build()
    {
        switch ($this->type) {

            case 'select':
                $where = (!empty($this->where)) ? ' WHERE ' . $this->where : '';
                $join = (!empty($this->join)) ? ' ' . $this->join : '';
                $orderBy = (!empty($this->orderBy)) ? ' ORDER BY ' . $this->orderBy : '';
                $this->where = $this->join = $this->orderBy = [];
                $this->query = $this->baseQuery . $join . $where . $orderBy;
                break;

            case 'insert':
                $this->query = $this->baseQuery . $this->buildInsertFieldsAndValues();
                break;

            case 'delete':
                if (empty($this->where))
                    throw new QueryManagerException('Where clause can\'t be empty');
                $where = ' WHERE ' . $this->where;
                $this->query = $this->baseQuery . $where;
                break;

            case 'update':
                if (empty($this->where))
                    throw new QueryManagerException('Where clause can\'t be empty');
                $where = ' WHERE ' . $this->where;
                $this->query = $this->baseQuery . $this->buildUpdateFieldsAndValues() . $where;
                break;

            default:
                throw new QueryManagerException('Unknown query type');
                break;
        }

        return $this;
    }

    public function execute()
    {
        $this->build();

        $result = null;
        try {

            switch ($this->type) {
                case 'select':
                    $result = Orm::getConnexion()->prepare($this->query);
                    $result->execute();
                    $result =  $result->fetchAll(\PDO::FETCH_ASSOC);
                    break;
                case 'insert':
                case 'delete':
                case 'update':
                    $result = Orm::getConnexion()->prepare($this->query)->execute();
                    break;
                default:
                    throw new QueryManagerException('Unknown query type');
                    break;
            }

        } catch (\Exception $e) {
            Orm::logError($this->query, $e);
        }

        Orm::logSql($this->query);

        return $result;
    }

    public function buildInsertFieldsAndValues()
    {
        $nameFields = [];
        $valueFields = [];
        foreach ($this->fields as $i => $v) {
            $nameFields[] = $i;
            $valueFields[] = '\''.$v.'\'';
        }

        $result = '(' . implode(', ',$nameFields) . ') VALUES (' . implode(', ',$valueFields) . ')';

        $this->fields = [];

        return $result;
    }

    public function buildUpdateFieldsAndValues()
    {
        $update = [];

        foreach ($this->fields as $i => $v) {
            $update[] = $i.' = \'' . $v . '\'';
        }

        $result = implode(', ', $update);

        return $result;
    }

    /** GENERIC PERSIST METHOD */
    public function persist($object)
    {
        $className = (new \ReflectionClass($object))->getShortName();

        $columns = $this->getTableColumns($className);
        $callable = [];
        $insert = true;

        foreach ($columns as $field) {

            $method = 'get' . ucfirst($field);

            if (method_exists($object, $method))
                $callable[$field] = $method;

            if (isset($callable[$field]) && $field !== 'password')
                if($this->exist($className, $field, $object->$callable[$field]()))
                    $insert = false;
        }

        if ($insert) {

            $this->insert($className);

            foreach ($columns as $field) {
                if (in_array($field, ['id', 'Id', 'ID', 'iD']))
                    continue;

                if (empty($object->$callable[$field]()))
                    throw new QueryManagerException('Vous devez renseigner tout les champs de la table');
                else
                    $this->addField($field, $object->$callable[$field]());
            }

            $result = $this->execute();

        } else {

            $this->update($className);

            foreach ($columns as $field) {
                if (in_array($field, ['id', 'Id', 'ID', 'iD']))
                    continue;

                if (!empty($object->$callable[$field]()))
                    $this->addField($field, $object->$callable[$field]());

            }

            $where = $object->getIsUniq().' = \''.$object->$callable[$object->getIsUniq()]().'\'';

            $this->where('id = '.$this->getItemById($className, $where));

            $result = $this->execute();
        }

        return $result;
    }

    public function getItemById($table, $where)
    {
        $query = 'SELECT id FROM '.$table.' WHERE ' . $where;
        $result = null;
        try {
            $result = Orm::getConnexion()->prepare($query);
            $result->execute();
            $result = $result->fetch(\PDO::FETCH_ASSOC)['id'];
        } catch (\Exception $e) {
            Orm::logError($query, $e);
        }

        Orm::logSql($query);

        return $result;
    }
}