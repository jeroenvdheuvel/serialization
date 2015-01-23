<?php
namespace jvdh\Serialization\Serializable;

use Countable;
use Iterator;
use jvdh\Serialization\Exception\PropertyNameAlreadyClaimedException;
use jvdh\Serialization\Exception\PropertyNameDoesNotExistException;

class Object implements Countable, Iterator // TODO: Check if countable is needed when iterator is implemented
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var array|ObjectProperty[]
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
        if ($this->doesPropertyWithNameExists($property->getName())) {
            throw new PropertyNameAlreadyClaimedException($property->getName());
        }

        $this->data[$property->getName()] = $property;
    }

    /**
     * @param mixed $name
     * @return mixed
     */
    public function getPropertyValueByName($name)
    {
        return $this->getPropertyByName($name)->getValue();
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function setPropertyValueByName($name, $value)
    {
        $this->getPropertyByName($name)->setValue($value);
    }

    /**
     * @param mixed $name
     * @return ObjectProperty
     * @throws PropertyNameDoesNotExistException
     */
    protected function getPropertyByName($name)
    {
        if (!$this->doesPropertyWithNameExists($name)) {
            throw new PropertyNameDoesNotExistException($name);
        }

        return $this->data[$name];
    }

    /**
     * @param mixed $name
     * @return bool
     */
    protected function doesPropertyWithNameExists($name)
    {
        return array_key_exists($name, $this->data);
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