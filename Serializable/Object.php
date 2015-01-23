<?php
namespace jvdh\Serialization\Serializable;

use ArrayAccess;
use Iterator;
use jvdh\Serialization\Exception\PropertyNameAlreadyClaimedException;

class Object //implements Iterator
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
}
