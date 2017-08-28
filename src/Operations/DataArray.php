<?php
namespace FiremonPHP\Database\Operations;


class DataArray
{
    /**
     * @var array
     */
    private $_data = [];

    /**
     * @var string
     */
    private $_indexOfData;

    /**
     * @var string
     */
    private $_namespace;

    /**
     * @var mixed
     */
    private $_id;

    /**
     * @var string
     */
    private $_actionType;

    public function __construct($data, $namespace)
    {
        if (!is_string($namespace)) {
            throw new \InvalidArgumentException('Structure data needed index string key on first level!');
        }

        $this->_data = $data;
        $this->_indexOfData = $namespace;

        $this->setPersonalInfo();
        $this->setActionType();
    }

    /**
     * @return string
     */
    public function action()
    {
        return $this->_actionType;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Check have sub data by types of sub content
     * If data is null, the main enter is a delete point
     * @return bool
     */
    public function haveSubData()
    {
        if (is_null($this->_data)) {
            return false;
        }

        $key = key($this->_data);
        if (is_null($this->_data[$key]) || is_array($this->_data[$key])) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Set personal info by '/' on string index
     * If found '/' the data is a filter. The action must be a update or delete
     */
    private function setPersonalInfo()
    {
        $barPosition = strpos($this->_indexOfData, '/');
        if ($barPosition > 0 || $barPosition === 0) {
            $this->_id = substr($this->_indexOfData, $barPosition+1);
            $this->_namespace = substr($this->_indexOfData, 0, $barPosition);
            return;
        }
        $this->_namespace = $this->_indexOfData;
    }

    private function setActionType()
    {
        if ($this->_id === null) {
            $this->_actionType = 'insert';
            return;
        }

        $this->_actionType = $this->getActionByDataType();
    }

    /**
     * If data is a null the action is a delete if not action is update =)
     * @return string
     */
    private function getActionByDataType()
    {
        if ($this->_data === null) {
            return 'delete';
        }

        return 'update';
    }
}