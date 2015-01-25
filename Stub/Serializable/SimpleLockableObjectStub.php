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
        parent::__construct('SimpleLockableObjectStub');

        $this->addProperty(new PublicObjectProperty('first public property', null));
        $this->addProperty(new PublicObjectProperty('second public property', false));

        $this->addProperty(new ProtectedObjectProperty('first protected property', -2));
        $this->addProperty(new ProtectedObjectProperty('second protected property', -5.1234));

        $this->addProperty(new PrivateObjectProperty('first private property', 'lorem ipsum'));

        $this->lock();
    }
}
