<?php
namespace jvdh\Serialization\Stub\Serializable;

use jvdh\Serialization\Serializable\LockableObject;

class EmptyLockableObjectStub extends LockableObject
{
    public function __construct()
    {
        parent::__construct('EmptyLockableObjectStub');

        $this->lock();
    }
} 