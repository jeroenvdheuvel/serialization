<?php
namespace jvdh\Serialization\Serializable;

use Countable;
use Iterator;
use jvdh\Serialization\Exception\PropertyNameAlreadyClaimedException;

class Object implements Countable, Iterator // TODO: Check if countable is needed when iterator is implemented
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
     * @param ObjectProperty $property
     */
    public function addProperty(ObjectProperty $property)
    {
        if (array_key_exists($property->getName(), $this->data)) {
            throw new PropertyNameAlreadyClaimedException($property->getName());
        }

        // TODO: Check if object is locked
        $this->data[$property->getName()] = $property;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        return next($this->data);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        $key = $this->key();
        return $key !== null && $key !== false;
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        reset($this->data);
    }
}


// TODO: Make it possible to lock the object
// TODO: Make it possible to get the property value
// TODO: Make it possible to set the property value