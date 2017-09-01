<?php
namespace FiremonPHP\Database\Operations;

use FiremonPHP\Connection\ManagerInterface;
use FiremonPHP\Database\Traits\OptionsTrait;

class Update
{
    use OptionsTrait;

    public static function update(ManagerInterface $manager, string $collecitonName, $dataUpdate, $collectionId, $options = [])
    {

        $conditions = self::getConditions($options, $collectionId);
        $options = self::getOptions($options, 'update');
        $manager->update($collecitonName, $dataUpdate, $conditions, $options);
    }
}