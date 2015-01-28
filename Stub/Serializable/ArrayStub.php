<?php
namespace jvdh\Serialization\Stub\Serializable;

class ArrayStub
{
    public $publicEmptyArray = array();
    protected $protectedArrayWithValues = array(1, '2', false);
    private $privateArrayWithKeysAndValues = array('key' => 'value', 'false' => false, 'null' => null);
}
