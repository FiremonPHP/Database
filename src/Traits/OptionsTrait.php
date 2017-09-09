<?php
namespace FiremonPHP\Database\Traits;

trait OptionsTrait
{
    private static function getOptions($options, string $actionType)
    {
        if (isset($options['options']['many']) && $actionType === 'update') {
            return ['multi' => true];
        }

        if (isset($options['options']['many']) && $actionType === 'delete') {
            return ['limit' => false];
        }

        if ($actionType === 'delete' && !isset($options['many'])) {
            return ['limit' => true];
        }

        return [];
    }

    private static function getConditions($options, $valueId)
    {
        if (isset($options['index'])) {
            return [
                'index' => $options['index'],
                'key' => $valueId
            ];
        }
        try {
            return [
                'index' => '_id',
                'key' => new \MongoDB\BSON\ObjectID($valueId)
            ];
        } catch (\Throwable $exception) {
            throw new \InvalidArgumentException('\''.$valueId.'\' Does not have an index set correctly, set it to a \'setIndex\' function!');
        }
    }
}