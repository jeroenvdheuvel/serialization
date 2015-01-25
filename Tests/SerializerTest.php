<?php
namespace jvdh\Serialization\Tests;

use jvdh\Serialization\Serializable\Object;
use jvdh\Serialization\SerializerInterface;
use jvdh\Serialization\Stub\Serializable\ArrayLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\EmptyLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\LockableObjectContainingAnotherLockableObjectStub;
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
        return [
            [new EmptyLockableObjectStub(), 'O:46:"jvdh\Serialization\Stub\Serializable\EmptyStub":0:{}'],
            [new SimpleLockableObjectStub(), "O:47:\"jvdh\\Serialization\\Stub\\Serializable\\SimpleStub\":5:{s:19:\"firstPublicProperty\";N;s:20:\"secondPublicProperty\";b:0;s:25:\"\0*\0firstProtectedProperty\";i:-2;s:26:\"\0*\0secondProtectedProperty\";d:-5.1234000000000002;s:69:\"\0jvdh\\Serialization\\Stub\\Serializable\\SimpleStub\0firstPrivateProperty\";s:11:\"lorem ipsum\";}"],
            [new ArrayLockableObjectStub(), "O:46:\"jvdh\\Serialization\\Stub\\Serializable\\ArrayStub\":3:{s:16:\"publicEmptyArray\";a:0:{}s:27:\"\0*\0protectedArrayWithValues\";a:3:{i:0;i:1;i:1;s:1:\"2\";i:2;b:0;}s:77:\"\0jvdh\\Serialization\\Stub\\Serializable\\ArrayStub\0privateArrayWithKeysAndValues\";a:3:{s:3:\"key\";s:5:\"value\";s:5:\"false\";b:0;s:4:\"null\";N;}}"],
            [new LockableObjectContainingAnotherLockableObjectStub(), "O:66:\"jvdh\\Serialization\\Stub\\Serializable\\ObjectContainingAnotherObject\":2:{s:79:\"\0jvdh\\Serialization\\Stub\\Serializable\\ObjectContainingAnotherObject\0emptyObject\";O:46:\"jvdh\Serialization\Stub\Serializable\EmptyStub\":0:{}s:15:\"\0*\0simpleObject\";O:47:\"jvdh\\Serialization\\Stub\\Serializable\\SimpleStub\":5:{s:19:\"firstPublicProperty\";N;s:20:\"secondPublicProperty\";b:0;s:25:\"\0*\0firstProtectedProperty\";i:-2;s:26:\"\0*\0secondProtectedProperty\";d:-5.1234000000000002;s:69:\"\0jvdh\\Serialization\\Stub\\Serializable\\SimpleStub\0firstPrivateProperty\";s:11:\"lorem ipsum\";}}"],
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
}
// TODO: Check if all flows are covered and can be simplfied
// TODO: Add more tests concerning references
