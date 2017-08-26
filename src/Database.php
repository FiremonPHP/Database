<?php
namespace FiremonPHP\Database;


use FiremonPHP\Database\Connection\ConnectionManager;

class Database
{
    /**
     * @var \FiremonPHP\Database\Connection\ConnectionInterface
     */
    private $_connection;

    public function __construct(\FiremonPHP\Database\Connection\ConnectionInterface $connection = null)
    {
        if ($connection === null) {
            $this->_connection = ConnectionManager::get('default');
        } elseif($connection instanceof \FiremonPHP\Database\Connection\ConnectionInterface) {
            $this->_connection = $connection;
        } else {
            throw new \ErrorException('Don\'t have Connection instacied yet!');
        }
    }

    /**
     * @param array $data
     * @return Query\WriteQuery
     */
    public function set(array $data)
    {
        return (new Query\WriteQuery($this->_connection, $data));
    }

    /**
     * @param $urlNamespace
     * @return Query\ReadQuery
     */
    public function get(string $urlNamespace)
    {
        return (new Query\ReadQuery($this->_connection, $urlNamespace));
    }
}