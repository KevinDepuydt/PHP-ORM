<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 09/12/2015
 * Time: 12:23
 */

namespace App\Interfaces;

interface QueryManagerInterface
{
    public function select($name, $field);
    public function delete($tablename);
    public function update($tablename);
    public function insert($tablename);
    public function count($tablename);
    public function exist($tablename, $field, $value);
    public function join($join);
    public function orderBy($orderBy);
    public function where($where);
}