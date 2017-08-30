<?php
namespace FiremonPHP\Database\Exceptions;


class DuplicateKeyException extends \Exception implements \MongoDB\Exception\Exception
{
    /**
     * @var mixed
     */
    private $index;

    /**
     * @var mixed
     */
    private $value;

    public function __construct(string $message, int $code = 11000)
    {

    }

    private function setValue()
    {

    }

    private function setIndex()
    {

    }
}