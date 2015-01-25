<?php
namespace jvdh\Serialization\Stub\Serializable;

use jvdh\Serialization\Serializable\LockableObject;
use jvdh\Serialization\Serializable\PrivateObjectProperty;
use jvdh\Serialization\Serializable\ProtectedObjectProperty;
use jvdh\Serialization\Serializable\PublicObjectProperty;

class ArrayLockableObjectStub extends LockableObject
{
    public function __construct()
    {
        parent::__construct('ArrayLockableObjectStub');

        $this->addProperty(new PublicObjectProperty('publicEmptyArray', []));
        $this->addProperty(new ProtectedObjectProperty('protectedArrayWithValues', [1, '2', false]));
        $this->addProperty(new PrivateObjectProperty('privateArrayWithKeysAndValues', ['key' => 'value', 'false' => false, 'null' => null]));

        $this->lock();
    }
}
