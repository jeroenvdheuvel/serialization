<?php
namespace jvdh\Serialization\Tests;

use jvdh\Serialization\Serializable\Object;
use jvdh\Serialization\Serializable\PrivateObjectProperty;
use jvdh\Serialization\Serializable\ProtectedObjectProperty;
use jvdh\Serialization\Serializable\PublicObjectProperty;
use jvdh\Serialization\NonNativeSerializer;
use jvdh\Serialization\SerializerInterface;
use jvdh\Serialization\Stub\Serializable\NonexistentObjectPropertyStub;
use stdClass;

abstract class SerializerTest extends \PHPUnit_Framework_TestCase
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
        return [
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
        ];
    }

    /**
     * @param mixed $data
     * @return array
     */
    private function getUnserializedDataWithExpectedSerializedDataAsArray($data)
    {
        return [$data, serialize($data)];
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
        return [
            $this->getUnserializedDataWithExpectedSerializedDataAsArray([]),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray([1]),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray([2,3]),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray([2 => 4]),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(['a' => 'b', 'c' => true, 1, 2.23, null]),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(['b' => [2, 5]]),
        ];
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

    public function getSerializedObjectData()
    {
        // TODO: Create stubs
        // TODO: - Empty
        // TODO: - With simple data
        // TODO: - With simple data and arrays
        // TODO: - With simple data and arrays and another serializable object
        // TODO: Test Serialize with unexisting class

        $serializedObject5 = new Object('jvdh\Serialization\Tests\SerializableStubWithPublicAndProtectedAndPrivateProperties');
        $serializedObject5->addProperty(new PrivateObjectProperty('privatePropertyOne', 0.1));
        $serializedObject5->addProperty(new ProtectedObjectProperty('protectedPropertyOne', 'one'));
        $serializedObject5->addProperty(new ProtectedObjectProperty('protectedPropertyTwo', null));
        $serializedObject5->addProperty(new PublicObjectProperty('publicPropertyOne', true));
        $serializedObject5->addProperty(new PublicObjectProperty('publicPropertyTwo', 12));

        return [
            [$serializedObject5, "O:83:\"jvdh\\Serialization\\Tests\\SerializableStubWithPublicAndProtectedAndPrivateProperties\":5:{s:103:\"\000jvdh\\Serialization\\Tests\\SerializableStubWithPublicAndProtectedAndPrivateProperties\000privatePropertyOne\";d:0.10000000000000001;s:23:\"\000*\000protectedPropertyOne\";s:3:\"one\";s:23:\"\000*\000protectedPropertyTwo\";N;s:17:\"publicPropertyOne\";b:1;s:17:\"publicPropertyTwo\";i:12;}"],
        ];
    }

    /**
     * @dataProvider getSerializedUnsupportedData
     *
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
        return [
            [fopen(__FILE__, 'r')],
            [new stdClass()],
        ];
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
     * @return SerializerInterface
     */
    abstract protected function getSerializer();
}

// TODO: Check if all flows are covered and can be simplfied
// TODO: Make another Serializer that uses Serialize() method of php when possible (not for arrays or objects)
// TODO: Add more tests concerning references