<?php
namespace jvdh\Serialization\Stub;

class ObjectWithPublicAndProtectedPropertiesStub extends ObjectWithPublicPropertiesStub
{
    protected $protectedPropertyOne = 'one';
    protected $protectedPropertyTwo = null;
}