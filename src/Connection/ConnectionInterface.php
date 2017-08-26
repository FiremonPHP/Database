<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 24/08/2017
 * Time: 23:09
 */

namespace FiremonPHP\Database\Connection;


interface ConnectionInterface
{
    public function executeQuery(string $type, array $queryData);
}