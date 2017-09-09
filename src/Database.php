<?php
namespace FiremonPHP\Database;


use FiremonPHP\Manager\Configuration;
use FiremonPHP\Manager\Queries\FindQuery;

class Database
{
    /**
     * @var \FiremonPHP\Manager\Manager
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
     * @param string $urlNamespace
     * @return FindQuery
     */
    public function get(string $urlNamespace)
    {
        return new FindQuery($urlNamespace, $this->_manager);
    }
}