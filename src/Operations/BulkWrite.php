<?php
namespace FiremonPHP\Database\Operations;


class BulkWrite
{
    /**
     * @var \MongoDB\Driver\BulkWrite
     */
    private $bulk;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var string
     */
    private $indexName;

    /**
     * @var string
     */
    private $databaseName;

    /**
     * BulkWrite constructor.
     * $writeData['option'][$namespace']
     * $writeData['indexes'][$namespace']
     * $writeData['data'] === all data blocks on set function
     * @param array $writeData
     */
    public function __construct(array $writeData, string $namespace, string $aliasDatabase)
    {
        $this->namespace = $namespace;
        $this->setIndex($writeData);
        $this->setOptions($writeData);
        $this->bulk = new \MongoDB\Driver\BulkWrite();
        $this->databaseName = $aliasDatabase;
        $this->loop($writeData);
    }

    /**
     * @return \MongoDB\Driver\BulkWrite
     */
    public function getBulk()
    {
        return $this->bulk;
    }

    /**
     * Return full namespace operation to executeBulkWrite
     * @return string
     */
    public function getFullNamespace()
    {
        return $this->databaseName.'.'.$this->namespace;
    }


    private function loop(array $dataWrite)
    {
        $firstKey = key($dataWrite);

        if (is_string($firstKey)) {
            $this->onFilterIndexKey($dataWrite[$firstKey], $firstKey);
        }

        if (is_int($firstKey)) {
            $this->bulk->insert($dataWrite[$firstKey]);
        }

        unset($dataWrite[$firstKey]);

        if (count($dataWrite) > 0) {
            $this->loop($dataWrite);
        }
    }

    /**
     * When index is filter type set action by type of data
     * @param array $dataWrite
     * @param $firstKey
     */
    private function onFilterIndexKey($dataWrite, $firstKey)
    {
        if ($dataWrite === null) {
            $this->bulk->delete(
                $this->getCondition($firstKey),
                ['limit' => 1]
            );
            return;
        }

        $this->bulk->update(
            $this->getCondition($firstKey),
            ['$set' => $dataWrite]
        );
    }

    /**
     * @param string $firstKey
     * @return array
     */
    private function getCondition(string $firstKey)
    {
        $firstKey = substr($firstKey, 1);
        if ($this->indexName === null) {
            return ['_id' => new \MongoDB\BSON\ObjectID($firstKey)];
        }

        return [$this->indexName => $firstKey];
    }

    /**
     * @param array $dataWrite
     */
    private function setIndex(array &$dataWrite)
    {
        if (isset($dataWrite['index'])) {
            $this->indexName = $dataWrite['index'];
            unset($dataWrite['index']);
        }
    }

    /**
     * @param array $dataWrite
     */
    private function setOptions(array &$dataWrite)
    {
        if (isset($dataWrite['options'])) {
            $this->options = $dataWrite['options'];
            unset($dataWrite['options']);
        }
    }

}