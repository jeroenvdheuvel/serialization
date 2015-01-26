<?php
namespace jvdh\Serialization\Stub\Serializable;

use jvdh\Serialization\Serializable\LockableObject;
use jvdh\Serialization\Serializable\PublicObjectProperty;

class ObjectContainingSimpleReferencesLockableObjectStub extends LockableObject
{
    public function __construct()
    {
        parent::__construct('jvdh\Serialization\Stub\Serializable\ObjectContainingSimpleReferencesStub');

        $firstValue = 'lorem ipsum dolem';
        $secondValue = 2;

        $this->addProperty(new PublicObjectProperty('publicFirstValue', $firstValue));
        $this->addProperty(new PublicObjectProperty('publicSecondValue', $secondValue));
        $this->addProperty(new PublicObjectProperty('publicThirdValue', $firstValue));
        $this->addProperty(new PublicObjectProperty('publicFourthValue', 4.0));
        $this->addProperty(new PublicObjectProperty('publicFifthValue', $secondValue));

        $this->lock();
    }
}
