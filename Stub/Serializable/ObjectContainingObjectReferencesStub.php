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
        $this->secondValue = new SimpleStub();
        $this->thirdValue = &$this->firstValue;
    }
}
