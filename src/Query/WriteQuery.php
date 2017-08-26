<?php
namespace FiremonPHP\Database\Query;


class WriteQuery implements QueryInterface
{
    /**
     * @var \FiremonPHP\Database\Connection\ConnectionInterface
     */
    private $_connection;

    /**
     * Index of namespaces
     * @var array
     */
    private $_indexes = [];

    /**
     * Default options to bulkWriter
     * @var array
     */
    private $_options = [
        'multi' => false,
        'upsert' => false
    ];

    private $_data = [];

    /**
     * Default writeConcern
     * @var array
     */
    private $_writeConcern = [

    ];

    public function __construct(\FiremonPHP\Database\Connection\ConnectionInterface $connection, array $data)
    {
        $this->_connection = $connection;
        $this->_data = $data;
    }

    /**
     * Set effects to many documments!
     * @param bool $many
     * @return $this
     */
    public function many(bool $many = true)
    {
        $this->_options['multi'] = $many;
        return $this;
    }

    public function replace(bool $replace = true)
    {
        $this->_options['upsert'] = $replace;
        return $this;
    }

    /**
     * @param string $collectionName
     * @param $value
     * @return $this
     */
    public function setIndex(string $collectionName, $value)
    {
        $this->_indexes[$collectionName] = $value;
        return $this;
    }

    public function execute()
    {
        return $this->_connection->executeQuery('write', [
            'data' => $this->_data,
            'indexes' => $this->_indexes,
            'options' => $this->_options
        ]);
    }

}