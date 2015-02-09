<?php
namespace jvdh\Serialization\Tests;

use stdClass;

abstract class AbstractSerializableTest extends \PHPUnit_Framework_TestCase
{
    protected function ensureNativeSerializeIsAbleToReturnReferencesToObjectsOrSkip()
    {
        $o = new stdClass();
        $nativeUnserializedData = unserialize(serialize(array(&$o, &$o)));
        $nativeUnserializedData[0] = null;
        if ($nativeUnserializedData[0] !== $nativeUnserializedData[1]) {
            $this->markTestSkipped('Native unserialize is not returning a reference. This is probably HHVM.');
        }
    }
}
