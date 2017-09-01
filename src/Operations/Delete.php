<?php
namespace FiremonPHP\Database\Operations;


use FiremonPHP\Connection\ManagerInterface;
use FiremonPHP\Database\Traits\OptionsTrait;

class Delete
{
    use OptionsTrait;
    public static function delete(ManagerInterface $manager, string $collectionName, $collectionId, $options = [])
    {
        $conditions = self::getConditions($options, $collectionId);
        $options = self::getOptions($options, 'delete');
        $manager->delete($collectionName, $conditions, $options);
    }
}