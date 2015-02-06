<?php
namespace jvdh\Serialization\Tests;

use jvdh\Serialization\Serializable\Object;
use jvdh\Serialization\Serializer;
use jvdh\Serialization\Stub\Serializable\ArrayLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\ArrayStub;
use jvdh\Serialization\Stub\Serializable\EmptyLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\EmptyStub;
use jvdh\Serialization\Stub\Serializable\ObjectContainingAnotherObject;
use jvdh\Serialization\Stub\Serializable\ObjectContainingAnotherObjectLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\NonexistentObjectPropertyStub;
use jvdh\Serialization\Stub\Serializable\SimpleLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\SimpleStub;
use stdClass;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getUnserializedSimpleData
     *
     * @param mixed $unserializedData
     * @param string $expectedData
     */
    public function testSerialize_withSimpleData($unserializedData, $expectedData)
    {
        $serializedData = $this->getSerializer()->serialize($unserializedData);

        $this->assertSame($expectedData, $serializedData);
    }

    /**
     * @return array
     */
    public function getUnserializedSimpleData()
    {
        return array(
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(null),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(123),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(-456),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(7.89),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(0.12),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(123456789.0),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(123456789.12),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(true),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(false),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray('string'),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray('true'),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray('1234567890'),
        );
    }

    /**
     * @param mixed $data
     * @return array
     */
    private function getUnserializedDataWithExpectedSerializedDataAsArray($data)
    {
        return array($data, serialize($data));
    }

    /**
     * @dataProvider getSerializedArrayData
     *
     * @param mixed $unserializedData
     * @param string $expectedData
     */
    public function testSerialize_withArray($unserializedData, $expectedData)
    {
        $serializedData = $this->getSerializer()->serialize($unserializedData);

        $this->assertEquals($expectedData, $serializedData);
    }

    /**
     * @return array
     */
    public function getSerializedArrayData()
    {
        return array(
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(array()),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(array(1)),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(array(2,3)),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(array(2 => 4)),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(array('a' => 'b', 'c' => true, 1, 2.23, null)),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(array('b' => array(2, 5))),
        );
    }

    /**
     * @dataProvider getSerializedObjectData
     *
     * @param mixed $unserializedData
     * @param string $expectedData
     */
    public function testSerialize_withObject($unserializedData, $expectedData)
    {
        $serializedData = $this->getSerializer()->serialize($unserializedData);

        $this->assertSame($expectedData, $serializedData);
    }

    /**
     * @return array
     */
    public function getSerializedObjectData()
    {
        return array(
            array(new EmptyLockableObjectStub(), serialize(new EmptyStub())),
            array(new SimpleLockableObjectStub(), serialize(new SimpleStub())),
            array(new ArrayLockableObjectStub(), serialize(new ArrayStub())),
            array(new ObjectContainingAnotherObjectLockableObjectStub(), serialize(new ObjectContainingAnotherObject())),
        );
    }

    /**
     * @dataProvider getSerializedUnsupportedData
     * @expectedException \jvdh\Serialization\Exception\UnsupportedDataTypeException
     *
     * @param mixed $unserializedData
     * @throws \Exception
     */
    public function testSerialize_withUnsupportedType($unserializedData)
    {
        $this->getSerializer()->serialize($unserializedData);
    }

    /**
     * @return array
     */
    public function getSerializedUnsupportedData()
    {
        return array(
            array(fopen(__FILE__, 'r')),
            array(new stdClass()),
        );
    }

    /**
     * @expectedException \jvdh\Serialization\Exception\UnsupportedPropertyTypeException
     */
    public function testSerialize_withUnsupportedObjectPropertyThrowsException()
    {
        $o = new Object('any object');
        $o->addProperty(new NonexistentObjectPropertyStub());

        $this->getSerializer()->serialize($o);
    }

    /**
     * @return Serializer
     */
    protected function getSerializer()
    {
        return new Serializer();
    }
}
