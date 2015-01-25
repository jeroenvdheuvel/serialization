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

        $this->addProperty(new PublicObjectProperty('firstPublicProperty', null));
        $this->addProperty(new PublicObjectProperty('secondPublicProperty', false));

        $this->addProperty(new ProtectedObjectProperty('firstProtectedProperty', -2));
        $this->addProperty(new ProtectedObjectProperty('secondProtectedProperty', -5.1234));

        $this->addProperty(new PrivateObjectProperty('firstPrivateProperty', 'lorem ipsum'));

        $this->lock();
    }
}
