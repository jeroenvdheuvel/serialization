<?php
namespace jvdh\Serialization\Tests;

use jvdh\Serialization\Serializable\Object;
use jvdh\Serialization\SerializerInterface;
use jvdh\Serialization\Stub\Serializable\ArrayLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\EmptyLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\EmptyStub;
use jvdh\Serialization\Stub\Serializable\ObjectContainingAnotherObjectLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\NonexistentObjectPropertyStub;
use jvdh\Serialization\Stub\Serializable\SimpleLockableObjectStub;
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

    /**
     * @return array
     */
    public function getSerializedObjectData()
    {
        // TODO: Instead of using the string, it's also possible to serialize the object
        return [
            [new EmptyLockableObjectStub(), serialize(new EmptyStub())],
            [new EmptyLockableObjectStub(), $this->getSerializedStub('EmptySerializedStub')],
            [new SimpleLockableObjectStub(), $this->getSerializedStub('SimpleSerializedStub')],
            [new ArrayLockableObjectStub(), $this->getSerializedStub('ArraySerializedStub')],
            [new ObjectContainingAnotherObjectLockableObjectStub(), $this->getSerializedStub('ObjectContainingSimpleReferencesSerializedStub')],
        ];
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

    /**
     * @return string
     */
    protected function getSerializedStub($name)
    {
        return include sprintf('%s/../Stub/Serializable/%s.php', __DIR__, $name);
    }
}
// TODO: Check if all flows are covered and can be simplfied
// TODO: Add more tests concerning references
