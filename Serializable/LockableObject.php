<?php
namespace jvdh\Serialization\Serializable;

use jvdh\Serialization\Exception\ObjectIsLockedException;

class LockableObject extends Object
{
    /**
     * @var bool
     */
    private $isLocked = false;

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->isLocked;
    }

    public function lock()
    {
        $this->isLocked = true;
    }

    /**
     * @param ObjectProperty $property
     * @throws ObjectIsLockedException
     */
    public function addProperty(ObjectProperty $property)
    {
        if ($this->isLocked()) {
            throw new ObjectIsLockedException($this->getClassName());
        }

        parent::addProperty($property);
    }
}
