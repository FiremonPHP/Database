<?php
namespace FiremonPHP\Database;


class Database
{
    /**
     * @var \FiremonPHP\Database\Connection\ConnectionInterface
     */
    private $_connection;

    public function __construct(\FiremonPHP\Database\Connection\ConnectionInterface $connection)
    {
        $this->_connection = $connection;
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