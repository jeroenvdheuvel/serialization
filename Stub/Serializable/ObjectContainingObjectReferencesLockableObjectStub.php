<?php
namespace jvdh\Serialization\Stub\Serializable;

use jvdh\Serialization\Serializable\LockableObject;
use jvdh\Serialization\Serializable\PublicObjectProperty;

class ObjectContainingObjectReferencesLockableObjectStub extends LockableObject
{
    public function __construct()
    {
        parent::__construct('jvdh\Serialization\Stub\Serializable\ObjectContainingObjectReferencesStub');

        $this->addProperty(new PublicObjectProperty('firstValue', new EmptyLockableObjectStub()));
        $this->addProperty(new PublicObjectProperty('secondValue', new SimpleLockableObjectStub()));
        $this->addProperty(new PublicObjectProperty('thirdValue', new EmptyLockableObjectStub()));

        $this->lock();
    }
}
