<?php
namespace jvdh\Serialization\Stub\Serializable;

use jvdh\Serialization\Serializable\LockableObject;
use jvdh\Serialization\Serializable\PrivateObjectProperty;
use jvdh\Serialization\Serializable\ProtectedObjectProperty;

class ObjectContainingAnotherObjectLockableObjectStub extends LockableObject
{
    public function __construct()
    {
        parent::__construct('jvdh\Serialization\Stub\Serializable\ObjectContainingAnotherObject');

        $emptyStub = new EmptyLockableObjectStub();
        $simpleStub = new SimpleLockableObjectStub();

        $this->addProperty(new PrivateObjectProperty('emptyObject', $emptyStub));
        $this->addProperty(new ProtectedObjectProperty('simpleObject', $simpleStub));

        $this->lock();
    }
} 