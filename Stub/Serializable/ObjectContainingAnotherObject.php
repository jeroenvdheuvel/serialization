<?php
namespace jvdh\Serialization\Stub\Serializable;

class ObjectContainingAnotherObject
{
    private $emptyObject;
    protected $simpleObject;

    public function __construct()
    {
        $this->emptyObject = new EmptyStub();
        $this->simpleObject = new SimpleStub();
    }
} 