<?php
namespace jvdh\Serialization\Stub\Serializable;

use jvdh\Serialization\Serializable\LockableObject;
use jvdh\Serialization\Serializable\PrivateObjectProperty;
use jvdh\Serialization\Serializable\ProtectedObjectProperty;
use jvdh\Serialization\Serializable\PublicObjectProperty;

class SimpleLockableObjectStub extends LockableObject
{
    public function __construct()
    {
        parent::__construct('jvdh\Serialization\Stub\Serializable\SimpleStub');

        $n = null;
        $b = false;
        $i = -2;
        $f = -5.1234;
        $s = 'lorem ipsum';

        $this->addProperty(new PublicObjectProperty('firstPublicProperty', $n));
        $this->addProperty(new PublicObjectProperty('secondPublicProperty', $b));

        $this->addProperty(new ProtectedObjectProperty('firstProtectedProperty', $i));
        $this->addProperty(new ProtectedObjectProperty('secondProtectedProperty', $f));

        $this->addProperty(new PrivateObjectProperty('firstPrivateProperty', $s));

        $this->lock();
    }
}
