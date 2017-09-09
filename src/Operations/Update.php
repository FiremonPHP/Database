<?php
namespace FiremonPHP\Database\Operations;

use FiremonPHP\Manager\Manager;
use FiremonPHP\Database\Traits\OptionsTrait;

class Update
{
    use OptionsTrait;

    public static function update(Manager $manager, string $collectionName, $dataUpdate, $collectionId, $options = [])
    {

        $conditions = self::getConditions($options, $collectionId);
        $manager
            ->update($collectionName, $dataUpdate)
            ->equalTo($conditions['index'], $conditions['key'])
            ->persist();
    }
}