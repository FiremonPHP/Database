<?php
namespace FiremonPHP\Database\Query;


class ReadQuery implements QueryInterface
{
    /**
     * @var \FiremonPHP\Database\Connection\ConnectionInterface
     */
    private $_connection;

    private $_options = [];

    private $_alias;

    private $_conditions = [];

    public function __construct(\FiremonPHP\Database\Connection\ConnectionInterface $connection, string $alias)
    {
        $this->_alias = $alias;
        $this->_connection = $connection;
    }

    /**
     * Set fields of documents
     * @param array $fieldsName
     */
    public function fields(array $fieldsName)
    {
        array_map(function($value){
            $this->_options['projection'][$value] = '1';
        }, $fieldsName);
        return $this;
    }

    public function ascBy(string $fieldName)
    {
        $this->_options['sort'][$fieldName] = '1';
        return $this;
    }

    public function descBy(string $fieldName)
    {
        $this->_options['sort'][$fieldName] = '-1';
        return $this;
    }

    public function limit(int $limitNumber)
    {
        $this->_options['limit'] = $limitNumber;
        return $this;
    }

    public function skip(int $skipNumber)
    {
        $this->_options['skip'] = $skipNumber;
        return $this;
    }

    public function notEqual(string $field, $value)
    {
        $this->_conditions[$field]['$ne'] = $value;
        return $this;
    }

    public function equalTo(string $field, $value)
    {
        $this->_conditions[$field]['$eq'] = $value;
        return $this;
    }

    public function startAt(string $field, $value)
    {
        $this->_conditions[$field]['$gte'] = $value;
        return $this;
    }

    public function endAt(string $field, $value)
    {
        $this->_conditions[$field]['$lt'] = $value;
        return $this;
    }

    /**
     * Execute querie
     * @return \MongoDB\Driver\Cursor
     */
    public function execute()
    {
        return $this->_connection->executeQuery('read',[
            'collection' => $this->_alias,
            'conditions' => $this->_conditions,
            'options' => $this->_options
        ]);
    }

}