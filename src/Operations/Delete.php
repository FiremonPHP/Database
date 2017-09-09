<?php
namespace FiremonPHP\Database\Operations;

use FiremonPHP\Manager\Manager;
use FiremonPHP\Database\Traits\OptionsTrait;

class Delete
{
    use OptionsTrait;
    public static function delete(Manager $manager, string $collectionName, $collectionId, $options = [])
    {
        $conditions = self::getConditions($options, $collectionId);
        $manager
            ->delete($collectionName)
            ->equalTo($conditions['index'], $conditions['key'])
            ->persist();
    }
}