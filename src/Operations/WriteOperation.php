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
        $this->iterateData($this->_data);

        if (count($this->_errors) > 0) {
            return false;
        }
        return true;
    }

    private function iterateData(array $data)
    {
        $firstKey = key($data);

        if (!$this->isFilterIndex($firstKey, $data)) {
            $this->insertData($firstKey, $data[$firstKey]);
        }

        unset($data[$firstKey]);

        if (count($data) > 0) {
            $this->iterateData($data);
        }
    }

    /**
     * @param $keyIndex
     * @param int $barPosition
     * @return bool|string
     */
    private function getIndex($keyIndex, int $barPosition)
    {
        return substr($keyIndex, $barPosition+1);
    }

    /**
     * @param $keyIndex
     * @param int $barPosition
     * @return bool|string
     */
    private function getNamespace($keyIndex, int $barPosition)
    {
        return substr($keyIndex, 0, $barPosition);
    }

    /**
     * If '/' founded on keyArray get id and namespace and execute setFilteredData
     * @param $keyIndex
     * @return bool
     */
    private function isFilterIndex($keyIndex, $data, string $nsOnManyDatas = null)
    {
        $barPosition = strpos($keyIndex, '/');
        if ($barPosition > 0 || $barPosition === 0) {
            $id = $this->getIndex($keyIndex, $barPosition);
            $namespace = $this->getNamespace($keyIndex, $barPosition);
            $namespace = $nsOnManyDatas ?? $namespace;
            $this->setFilteredData($id, $namespace, $data[$keyIndex]);
            return true;
        }
        return false;
    }

    /**
     * If id and namespace not false execute
     * If data is null -> go delete action
     * else go -> update action
     * @param $id
     * @param $namespace
     * @param $data
     */
    private function setFilteredData($id, $namespace, $data)
    {
        if ($id === false || $namespace === false) {
            $this->_errors[] = [
                'error' => 'Id or Namespace not found on array data!',
                'data' => $data,
                'id' => $id,
                'collection' => $namespace
            ];
            return;
        }

        if ($data === null) {
            $this->delete($id, $namespace);
            return;
        }

        $this->update($id, $namespace, $data);
    }

    /**
     * If data have one insert data
     * If not, execute basic operation on second level of array.
     * @param $namespace
     * @param $data
     */
    private function insertData($namespace, $data)
    {
        $firstKey = key($data);
        if (!$this->isFilterIndex($firstKey, $data, $namespace)) {
            $this->insert($data[$firstKey], $namespace);
        }

        unset($data[$firstKey]);

        if (count($data) > 0) {
            $this->insertData($namespace, $data);
        }
    }

    /**
     * Set insert action on namespace bulk
     * @param array $data
     * @param $namespace
     */
    private function insert(array $data, $namespace)
    {
        if (!isset($this->_bulk[$namespace])) {
            $this->_bulk[$namespace] = new \MongoDB\Driver\BulkWrite();
        }
        //Todo: implement validations methods!
        $this->_bulk[$namespace]->insert($data);
    }

    /**
     * Set update action on namespace bulk
     * @param $key
     * @param $namespace
     * @param $data
     */
    private function update($key, $namespace, $data)
    {
        //Todo: implement validations methods!
        if (!isset($this->_bulk[$namespace])) {
            $this->_bulk[$namespace] = new \MongoDB\Driver\BulkWrite();
        }
        $conditions = isset($this->_indexes[$namespace]) ? [$this->_indexes[$namespace] => $key] : ['_id' => new \MongoDB\BSON\ObjectID($key)];
        $this->_bulk[$namespace]->update($conditions, ['$set' => $data], $this->_options);
    }

    /**
     * Set delete action on namespace bulk
     * If not have index to namespace, set default mongodb index '_id'
     * @param $key
     * @param $namespace
     */
    private function delete($key, $namespace)
    {
        if (!isset($this->_bulk[$namespace])) {
            $this->_bulk[$namespace] = new \MongoDB\Driver\BulkWrite();
        }
        $conditions = isset($this->_indexes[$namespace]) ? [$this->_indexes[$namespace] => $key] : ['_id' => new \MongoDB\BSON\ObjectID($key)];
        $this->_bulk[$namespace]->delete($conditions, ['limit' => 1]);
    }

}