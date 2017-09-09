<?php
namespace FiremonPHP\Database\Operations;


use FiremonPHP\Manager\Manager;

class Insert
{
    public static function insert(Manager $manager, string $collectionName, $dataInsert)
    {
        $manager->insert($collectionName, $dataInsert);
    }
}