<?php
/**
 * Created by IntelliJ IDEA.
 * User: jeroen
 * Date: 25-1-2015
 * Time: 15:15
 */

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
 