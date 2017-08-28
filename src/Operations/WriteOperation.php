<?php
namespace FiremonPHP\Database\Operations;


class WriteOperation
{
    private $_data = [];

    /**
     * @var \MongoDB\Driver\BulkWrite[]
     */
    private $_bulk = [];

    private $_options = [];

    private $_concerns = [];

    private $_validations = [];

    private $_errors = [];

    private $_indexes = [];

    /**
     * WriteOperation constructor.
     * @param array $data
     * @param array $indexes
     * @param array $options
     *
     * [
    'data' => $this->_data,
    'indexes' => $this->_indexes,
    'options' => $this->_options
    ]
     */
    public function __construct(array $data, array $indexes, array $options = [])
    {
        $this->_data = $data;
        $this->_indexes = $indexes;
        $this->_options = $options;
    }

    /**
     * @return \MongoDB\Driver\BulkWrite[]
     */
    public function getBulkWrite()
    {
        return $this->_bulk;
    }

    public function run()
    {
        $this->iterateMainData($this->_data);

        if (count($this->_errors) > 0) {
            return false;
        }
        return true;
    }

    /**
     * @param array $data
     */
    private function iterateMainData(array $data)
    {
        $firstKey = key($data);

        $dataArray = new DataArray($data[$firstKey], $firstKey);

        if ($dataArray->haveSubData()) {
            $this->iterateSubData($dataArray->getData(), $dataArray->getNamespace());
        } else {
            $this->{$dataArray->action()}($dataArray);
        }

        unset($data[$firstKey]);
        unset($dataArray);

        if (count($data) > 0) {
            $this->iterateMainData($data);
        }
    }

    private function iterateSubData(array $data, $namespace)
    {
        $firstKey = key($data);
        $namespaceSubData = $namespace;
        if (is_string($firstKey) || is_null($firstKey)) {
            $namespaceSubData .= $firstKey;
        }
        $subDataArray = new DataArray($data[$firstKey], $namespaceSubData);
        $this->{$subDataArray->action()}($subDataArray);

        unset($data[$firstKey]);
        unset($subDataArray);

        if (count($data) > 0) {
            $this->iterateSubData($data, $namespace);
        }
    }


    /**
     * Set insert action on namespace bulk
     * @param array $data
     * @param $namespace
     */
    private function insert(DataArray $object)
    {
        $this->createBulkIfNotExist($object->getNamespace());

        //Todo: implement validations methods!
        $this->_bulk[$object->getNamespace()]->insert($object->getData());
    }

    private function createBulkIfNotExist(string $namespace)
    {
        if (!isset($this->_bulk[$namespace])) {
            $this->_bulk[$namespace] = new \MongoDB\Driver\BulkWrite();
        }
    }

    private function getIndexCondition(string $namespace, $objectId)
    {
        if (isset($this->_indexes[$namespace])) {
            return [$this->_indexes[$namespace] => $objectId];
        }
        return ['_id' => new \MongoDB\BSON\ObjectID($objectId)];
    }

    /**
     * Set update action on namespace bulk
     * @param $key
     * @param $namespace
     * @param $data
     */
    private function update(DataArray $object)
    {
        //Todo: implement validations methods!
        $this->createBulkIfNotExist($object->getNamespace());

        $conditions = $this->getIndexCondition($object->getNamespace(), $object->getId());
        $this->_bulk[$object->getNamespace()]->update($conditions, ['$set' => $object->getData()], $this->_options[$object->getNamespace()] ?? []);
    }

    /**
     * Set delete action on namespace bulk
     * If not have index to namespace, set default mongodb index '_id'
     * @param $key
     * @param $namespace
     */
    private function delete(DataArray $object)
    {
        $this->createBulkIfNotExist($object->getNamespace());

        $conditions = $this->getIndexCondition($object->getNamespace(), $object->getId());
        $this->_bulk[$object->getNamespace()]->delete($conditions, ['limit' => 1]);
    }

}