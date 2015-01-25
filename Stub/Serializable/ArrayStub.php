<?php
namespace jvdh\Serialization\Stub\Serializable;

class ArrayStub
{
    public $publicEmptyArray = [];
    protected $protectedArrayWithValues = [1, '2', false];
    private $privateArrayWithKeysAndValues = ['key' => 'value', 'false' => false, 'null' => null];
}
