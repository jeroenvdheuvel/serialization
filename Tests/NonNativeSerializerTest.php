<?php
namespace jvdh\Serialization\Tests;

use jvdh\Serialization\NonNativeSerializer;
use jvdh\Serialization\SerializerInterface;

class NonNativeSerializerTest extends SerializerTest
{
    /**
     * @return SerializerInterface
     */
    protected function getSerializer()
    {
        return new NonNativeSerializer();
    }
}
 