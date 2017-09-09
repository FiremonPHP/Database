<?php
namespace FiremonPHP\Database\Operations;


use FiremonPHP\Manager\Manager;

class WriteOperations
{
    /**
     * @var Manager
     */
    private $_manager;

    /**
     * @var array
     */
    private $_options;

    public function __construct(array $writeData, array $options = [], Manager $manager = null)
    {
        $this->_options = $options;
        $this->_manager = $manager;
        $self = $this;
        array_walk($writeData, function($collectionData, $collectionName) use(&$self){
            $self->isInvalidCollectionName($collectionName);
            $self->interceptor($collectionName, $collectionData);
        });
    }

    public function execute()
    {
        return $this->_manager->execute();
    }

    /**
     * Intercepts datas and dispatch action by key's and data types
     * @param string $collectionName
     * @param $collectionData
     */
    private function interceptor(string $collectionName, $collectionData)
    {
        $collection = null;
        $index = null;
        extract($this->splitColelctionAndKey($collectionName));

        if ($index === null && $this->haveManyInsertData($collectionData)) {
            $this->interceptManyData($collectionName, $collectionData);
            return;
        }

        $this->dispatchAction($collection, $index, $collectionData);
    }

    /**
     * Dispatch Write operations
     * @param string $collectionName
     * @param null $collectionId
     * @param $collectionData
     */
    private function dispatchAction(string $collectionName, $collectionId = null, $collectionData)
    {
        if (
            $collectionId !== null &&
            $collectionData === null
        ) {
            Delete::delete(
                $this->_manager,
                $collectionName,
                $collectionId,
                $this->getCollectionOptions($collectionName)
            );
            return;
        }

        if ($collectionId !== null) {
            Update::update(
                $this->_manager,
                $collectionName,
                $collectionData,
                $collectionId,
                $this->getCollectionOptions($collectionName)
            );
            return;
        }

        Insert::insert($this->_manager, $collectionName, $collectionData);

    }

    /**
     * Get options and index from collection
     * @param string $collectionName
     * @param $key
     * @return array+
     */
    private function getCollectionOptions(string $collectionName)
    {
        $options = [];
        if (isset($this->_options['indexes'][$collectionName])) {
            $options['index'] = $this->_options['indexes'][$collectionName];
        }

        if (isset($this->_options['options'][$collectionName])) {
            $options['options'] = $this->_options['options'][$collectionName];
        }
        return $options;
    }

    /**
     * When data have sub tree with associative keys or added keys, this function can dismount all data
     * and set key by key data on collection
     * @param string $collectionName
     * @param array $collectionData
     */
    private function interceptManyData(string $collectionName, array $collectionData)
    {
        $self = $this;
        array_walk($collectionData, function($data, $indexData) use (&$self, $collectionName){
            if (is_string($indexData)) {
                $self->dispatchAction($collectionName, substr($indexData, 1), $data);
                return;
            }

            $self->dispatchAction($collectionName, null, $data);
        });
    }

    /**
     * Check if data is a sub tree
     * @param array $collectionData
     * @return bool
     */
    private function haveManyInsertData(array $collectionData)
    {
        $firstKey = key($collectionData);
        return is_array($collectionData[$firstKey]);
    }

    /**
     * @param string $key
     * @return array
     */
    private function splitColelctionAndKey(string $key)
    {
        $result = explode('/', $key);
        if (count($result) === 2) {
            return [
              'collection' => $result[0],
              'index' => $result[1]
            ];
        }

        return [
            'collection' => $key,
            'index' => null
        ];
    }

    /**
     * Check if key is a string
     * @param $key
     */
    private function isInvalidCollectionName($key)
    {
        if (is_string($key)) {
            return;
        }

        throw new \InvalidArgumentException('Data structure contain a non associative array!');
    }
}