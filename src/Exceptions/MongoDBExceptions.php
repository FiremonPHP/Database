<?php
/**
 * Created by PhpStorm.
 * User: Marcos
 * Date: 30/08/2017
 * Time: 16:02
 */

namespace FiremonPHP\Database\Exceptions;


class MongoDBExceptions
{
    private static $exception_class = [
        11000 => '\\FiremonPHP\\Database\\Exceptions\\DuplicateKeyException'
    ];

    /**
     * @param \MongoDB\Driver\WriteResult $writeResult
     * @return array
     */
    public static function get(\MongoDB\Driver\WriteResult $writeResult)
    {
        $errors = [];
        array_map(function(\MongoDB\Driver\WriteError $error) use($errors){
            $errors[] = self::$exception_class[$error->getCode()];
        }, $writeResult->getWriteErrors());
        return $errors;
    }
}