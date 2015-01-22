<?php
namespace jvdh\Serialization\Tests;

class SerializableStubWithPublicAndProtectedProperties extends SerializableStubWithPublicProperties
{
    protected $protectedPropertyOne = 'one';
    protected $protectedPropertyTwo = null;
}