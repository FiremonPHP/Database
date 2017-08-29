<?php
namespace FiremonPHP\Database\Operations;


class WriteOperation
{
    /**
     * @var BulkWrite[]
     */
    private $bulks = [];

    /**
     * @var array
     */
    private $writeData = [];

    public function __construct(array $data, array $options, array $indexes, string $aliasDatabase)
    {
        $this->loop($data);
        $this->organizeOptionsData($options);
        $this->organizeIndexesData($options);
        $this->setBulks($aliasDatabase);
    }

    /**
     * @return BulkWrite[]
     */
    public function getBulks(): array
    {
        return $this->bulks;
    }

    /**
     * Iterate all key of object and set new BulkWrites
     * @param array $data
     */
    private function loop(array $data)
    {
        $firstKey = key($data);

        if (!is_string($firstKey)) {
            throw new \InvalidArgumentException('Structure data invalid, all data need init by string key!');
        }

        $this->organizeDataStructure($data[$firstKey], $firstKey);
        unset($data[$firstKey]);

        if (count($data) > 0) {
            $this->loop($data);
        }

    }

    /**
     * Organize all data on right namespace by firstKeyIndex on data structure.
     * @param array $data
     * @param $firstKeyArray
     */
    private function organizeDataStructure($data, $firstKeyArray)
    {
        $matches = explode('/',$firstKeyArray);
        if (isset($matches[1])) {
            $this->writeData[$matches[0]]['/'.$matches[1]] = $data;
            return;
        }

        if ($data === null) {
            throw new \InvalidArgumentException('Data on key '.$firstKeyArray.' cannot be null!');
        }

        $subDataKey = key($data);
        if (is_string($data[$subDataKey])) {
            $this->writeData[$matches[0]][] = $data;
            return;
        }

        if (isset($this->writeData[$matches[0]])) {
            $this->writeData[$matches[0]] = array_merge($this->writeData[$matches[0]], $data);
            return;
        }

        $this->writeData[$matches[0]] = $data;
    }

    /**
     * Set all options to right namespace
     * @param array $options
     */
    private function organizeOptionsData(array $options)
    {
        $self = $this;
        array_walk($options, function($item, $key) use ($self) {
           $this->writeData[$key]['options'] = $item;
        });
    }

    /**
     * Set index of collection for filter conditions
     * @param array $indexes
     */
    private function organizeIndexesData(array $indexes)
    {
        $self = $this;
        array_walk($indexes, function($item, $key) use ($self) {
           $self->writeData[$key]['index'] = $item;
        });
    }

    /**
     * Iterate all datas structure seteds and create BulkWrite object
     * @param string $aliasDatabase
     */
    private function setBulks(string $aliasDatabase)
    {
        $self = $this;
        array_walk($this->writeData, function($item, $key) use ($self, $aliasDatabase){
            $self->bulks[$key] = new BulkWrite($item, $key, $aliasDatabase);
        });
    }
}