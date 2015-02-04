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

        $emptyArray = array();
        $simpleArray = array(1, '2', false);
        $associativeArray = array('key' => 'value', 'false' => false, 'null' => null);

        $this->addProperty(new PublicObjectProperty('publicEmptyArray', $emptyArray));
        $this->addProperty(new ProtectedObjectProperty('protectedArrayWithValues', $simpleArray));
        $this->addProperty(new PrivateObjectProperty('privateArrayWithKeysAndValues', $associativeArray));

        $this->lock();
    }
}
