<?php
namespace jvdh\Serialization\Stub\Serializable;

class ObjectContainingObjectReferencesStub
{
    public $firstValue;
    public $secondValue;
    public $thirdValue;

    public function __construct()
    {
        $this->firstValue = new EmptyStub();
        $this->secondValue = new SimpleLockableObjectStub();
        $this->thirdValue = &$this->firstValue;
    }
}
