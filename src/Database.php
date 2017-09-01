<?php
namespace FiremonPHP\Database;


use FiremonPHP\Connection\Configuration;

class Database
{
    /**
     * @var \FiremonPHP\Connection\ManagerInterface
     */
    private $_manager;

    public function __construct(string $connectionName = 'default')
    {
        $this->_manager = Configuration::get($connectionName);
    }

    /**
     * @param array $data
     * @return Query\WriteQuery
     */
    public function set(array $data)
    {
        return (new Query\WriteQuery($this->_manager, $data));
    }

    /**
     * @param $urlNamespace
     * @return Query\ReadQuery
     */
    public function get(string $urlNamespace)
    {
        return (new Query\ReadQuery($this->_manager, $urlNamespace));
    }
}