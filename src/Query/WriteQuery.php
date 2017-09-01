<?php
namespace FiremonPHP\Database\Query;


use FiremonPHP\Database\Operations\WriteOperations;

class WriteQuery
{
    /**
     * @var \FiremonPHP\Connection\ManagerInterface
     */
    private $_manager;

    /**
     * Index of namespaces
     * @var array
     */
    private $_indexes = [];

    /**
     * Default options to bulkWriter
     * @var array
     */
    private $_options = [];

    /**
     * @var array
     */
    private $_data = [];

    public function __construct(\FiremonPHP\Connection\ManagerInterface $manager, array $data)
    {
        $this->_manager = $manager;
        $this->_data = $data;
    }

    /**
     * Set effects to many documments!
     * @param bool $many
     * @return $this
     */
    public function setMany(string $collectionName,bool $many = true)
    {
        $this->_options[$collectionName]['many'] = $many;
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

    /**
     * Execute queries by array data structure
     */
    public function execute()
    {
        $writeOperation = new WriteOperations(
            $this->_data,
            [
                'indexes' => $this->_indexes,
                'options' => $this->_options
            ],
            $this->_manager
        );

        return $writeOperation->execute();
    }

}