<?php
namespace FiremonPHP\Database\Exceptions;


use SebastianBergmann\CodeCoverage\Report\PHP;

class DuplicateKeyException
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
        $cropedMessage = substr($message, strrpos($message,'index:'));
        $this->setValue($cropedMessage);
        $this->setIndex($cropedMessage);
    }

    private function setValue(string $message)
    {
        $matches = [];
        preg_match('/"(.*?)"/', $message, $matches);
        $this->value = $matches[1];
    }

    private function setIndex(string  $message)
    {
        $matches = [];
        preg_match('/index:\ (.*?)\ /', $message, $matches);
        $this->index = $matches[1];
    }
}