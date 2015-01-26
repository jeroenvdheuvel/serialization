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

        $this->addProperty(new PrivateObjectProperty('emptyObject', new EmptyLockableObjectStub()));
        $this->addProperty(new ProtectedObjectProperty('simpleObject', new SimpleLockableObjectStub()));

        $this->lock();
    }
} 