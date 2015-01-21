<?php
namespace jvdh\Serialization;

class TestClassThatCanBeSerializedStub
{
    private $privateValue;
    protected $protectedValue = 12345;
    public $publicValue;

    public function __construct($value)
    {
        $this->privateValue = $value;
        $this->publicValue = rand(0, 100);
    }
}