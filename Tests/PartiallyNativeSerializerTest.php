<?php
namespace jvdh\Serialization\Tests;

use jvdh\Serialization\PartiallyNativeSerializer;
use jvdh\Serialization\SerializerInterface;

class PartiallyNativeSerializerTest extends SerializerTest
{
    /**
     * @return SerializerInterface
     */
    protected function getSerializer()
    {
        return new PartiallyNativeSerializer();
    }
}
 