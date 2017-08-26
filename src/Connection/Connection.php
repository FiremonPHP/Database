<?php
namespace FiremonPHP\Database\Connection;


final class Connection implements ConnectionInterface
{
    /**
     * @var string
     */
    private $_alias;

    /**
     * @var \MongoDB\Driver\Manager
     */
    private $_manager;

    public function __construct(string $urlCon, string $alias, array $optionsCon = [])
    {
        $this->_manager = new \MongoDB\Driver\Manager($urlCon, $optionsCon);
        $this->_alias = $alias;
    }

    /**
     * @param string $type
     * @param string $collectionName
     * @param $query
     * @return \MongoDB\Driver\Cursor|\MongoDB\Driver\WriteResult
     * @throws \ErrorException
     */
    public function executeQuery(string $type, array $queryData)
    {
        return $this->{'_'.$type}($queryData);
    }

    /**
     * @param string $collectionName
     * @param array $queryData
     */
    private function _write(array $queryData)
    {
        $writeOperation = new \FiremonPHP\Database\Operations\WriteOperation(
            $queryData['data'],
            $queryData['indexes'],
            $queryData['options']
        );

        $writeOperation->run();

        foreach ($writeOperation->getBulkWrite() as $key => $bulk)
        {
            $namespace = $this->_alias.'.'.$key;
            $this->_manager->executeBulkWrite($namespace, $bulk);
        }


    }

    /**
     * @param string $collectionName
     * @param array $queryData
     * @return \MongoDB\Driver\Cursor
     */
    private function _read(array $queryData)
    {
        $namespace = $this->_alias.'.'.$queryData['collection'];
        $query = new \MongoDB\Driver\Query($queryData['conditions'], $queryData['options']);
        return $this->_manager->executeQuery( $namespace, $query);
    }

}