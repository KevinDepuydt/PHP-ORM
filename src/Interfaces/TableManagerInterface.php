<?php
/**
 * Created by PhpStorm.
 * User: Kévin
 * Date: 02/12/2015
 * Time: 15:02
 */

namespace App\Interfaces;

interface TableManagerInterface
{
    public function create();
    public function edit();
    public function delete();
    public function addField($name, $type); // @return $this
    public function removeField($name);
}