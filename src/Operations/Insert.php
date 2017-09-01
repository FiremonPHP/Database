<?php
namespace FiremonPHP\Database\Operations;


use FiremonPHP\Connection\ManagerInterface;

class Insert
{
    public static function insert(ManagerInterface $manager, string $collectionName, $dataInsert)
    {
        $manager->insert($collectionName, $dataInsert);
    }
}