<?php
namespace jvdh\Serialization\Serializable;

use ArrayAccess;

class SerializableObject implements ArrayAccess
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var array
     */
    private $data;

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
        $this->data = [];
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return array
     */
    public function getDataAsArray()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        return $this->data[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
