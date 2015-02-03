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
        parent::__construct('jvdh\Serialization\Stub\Serializable\ArrayStub');

        $this->addProperty(new PublicObjectProperty('publicEmptyArray', $a = array()));
        $this->addProperty(new ProtectedObjectProperty('protectedArrayWithValues', $a = array(1, '2', false)));
        $this->addProperty(new PrivateObjectProperty('privateArrayWithKeysAndValues', $a = array('key' => 'value', 'false' => false, 'null' => null)));

        $this->lock();
    }
}
