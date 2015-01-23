<?php
namespace jvdh\Serialization\Stub;

class StubObjectWithPublicAndProtectedProperties extends StubObjectWithPublicProperties
{
    protected $protectedPropertyOne = 'one';
    protected $protectedPropertyTwo = null;
}