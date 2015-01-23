<?php
namespace jvdh\Serialization\Tests;

class SerializableStubWithPublicAndProtectedAndPrivateProperties extends SerializableStubWithPublicAndProtectedProperties
{
    private $privatePropertyOne = 0.1;
}