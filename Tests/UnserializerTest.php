<?php
namespace jvdh\Serialization\Tests;

use Exception;
use jvdh\Serialization\Serializable\LockableObject;
use jvdh\Serialization\Serializable\Object as SerializableObject;
use jvdh\Serialization\Serializable\ObjectProperty;
use jvdh\Serialization\Stub\Serializable\ArrayLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\ArrayStub;
use jvdh\Serialization\Stub\Serializable\EmptyLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\EmptyStub;
use jvdh\Serialization\Stub\Serializable\LockableObjectContainingAnotherLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\ObjectContainingAnotherObject;
use jvdh\Serialization\Stub\Serializable\ObjectContainingObjectReferencesLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\ObjectContainingObjectReferencesStub;
use jvdh\Serialization\Stub\Serializable\ObjectContainingSimpleReferencesLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\ObjectContainingSimpleReferencesStub;
use jvdh\Serialization\Stub\Serializable\SimpleLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\SimpleStub;
use jvdh\Serialization\Unserializer;

class UnserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSerializedSimpleData
     *
     * @param string $serializedData
     * @param string $expectedData
     */
    public function testUnserialize_withSimpleData($serializedData, $expectedData)
    {
        $unserializer = new Unserializer();
        $unserializedData = $unserializer->unserialize($serializedData);

        $this->assertSame($expectedData, $unserializedData);
    }

    /**
     * @return array
     */
    public function getSerializedSimpleData()
    {
        return [
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(true),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(null),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray('true'),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray('0123456789'),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(123),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(1.23),
        ];
    }

    /**
     * @dataProvider getSerializedArrayData
     *
     * @param mixed$serializedData
     * @param string $expectedData
     * @throws \Exception
     */
    public function testUnserialize_withArray($serializedData, $expectedData)
    {
        $unserializer = new Unserializer();
        $unserializedData = $unserializer->unserialize($serializedData);

        $this->assertEquals($expectedData, $unserializedData);
    }

    public function getSerializedArrayData()
    {
        return [
            $this->getSerializedDataWithExpectedUnserializedDataAsArray([]),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(['0123456789', 1]),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray([2, 4, 1234]),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray([123, 456]),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray([123, 456, '789']),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(['abc' => 123]),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(['abc' => 'qwe']),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(['abc' => 'qwe', 4 => 123, 5 => 'aab']),
        ];
    }

    /**
     * @dataProvider getSerializedObjectData
     *
     * @param string $serializedData
     * @param mixed $expectedData
     * @throws Exception
     */
    public function testUnserialize_withObject($serializedData, LockableObject $expectedData)
    {
        $unserializer = new Unserializer();
        $unserializedData = $unserializer->unserialize($serializedData);

        $this->assertEqualsLockableObject($expectedData, $unserializedData);
    }

    /**
     * @param LockableObject|ObjectProperty[] $expected
     * @param LockableObject|ObjectProperty[] $given
     */
    private function assertEqualsLockableObject(LockableObject $expected, LockableObject $given)
    {
        $this->assertSame($expected->getClassName(), $given->getClassName());
        $this->assertSame($expected->isLocked(), $given->isLocked());
        $this->assertSame($expected->count(), $given->count());

        $this->assertObjectProperties($expected, $given);
    }

    /**
     * @param SerializableObject $expected
     * @param SerializableObject $given
     */
    private function assertObjectProperties(SerializableObject $expected, SerializableObject $given)
    {
        $expectedProperties = $this->getPropertiesOfLockableObject($expected);
        $givenProperties = $this->getPropertiesOfLockableObject($given);

        foreach ($expectedProperties as $key => $expectedProperty) {
            $this->assertTrue(array_key_exists($key, $givenProperties));
            $this->assertEqualsObjectProperty($expectedProperty, $givenProperties[$key]);
        }
    }

    /**
     * @param ObjectProperty $expected
     * @param ObjectProperty $given
     */
    private function assertEqualsObjectProperty(ObjectProperty $expected, ObjectProperty $given)
    {
        $this->assertSame($expected->getName(), $given->getName());
        $this->assertSame($expected->getType(), $given->getType());

        if ($expected->getValue() instanceof SerializableObject) {
            $this->assertEqualsLockableObject($expected->getValue(), $given->getValue());
        } else {
            $this->assertSame($expected->getValue(), $given->getValue());
        }
    }

    /**
     * @param LockableObject|ObjectProperty[] $object
     * @return array|ObjectProperty[]
     */
    private function getPropertiesOfLockableObject(LockableObject $object)
    {
        $properties = [];

        foreach ($object as $key => $property) {
            $properties[$key] = $property;
        }

        return $properties;
    }

    /**
     * @return array
     */
    public function getSerializedObjectData()
    {
        return [
            [serialize(new EmptyStub()), new EmptyLockableObjectStub()],
            [serialize(new SimpleStub()), new SimpleLockableObjectStub()],
            [serialize(new ArrayStub()), new ArrayLockableObjectStub()],
            [serialize(new ObjectContainingAnotherObject()), new LockableObjectContainingAnotherLockableObjectStub()],
            [serialize(new ObjectContainingSimpleReferencesStub()), new ObjectContainingSimpleReferencesLockableObjectStub()],
            [serialize(new ObjectContainingObjectReferencesStub()), new ObjectContainingObjectReferencesLockableObjectStub()],
        ];
    }

    /**
     * @param mixed$data
     * @return array
     */
    private function getSerializedDataWithExpectedUnserializedDataAsArray($data)
    {
        return [serialize($data), $data];
    }

    /**
     * @expectedException \jvdh\Serialization\Exception\UnsupportedSerializedVariableTypeException
     */
    public function testUnserialize_withUnknowntypeThrowsException()
    {
        $unserializer = new Unserializer();
        $unserializer->unserialize('Q:123');
    }

    /**
     * @dataProvider getSerializedDataWithReference
     *
     * @param string $serializedData
     * @throws Exception
     */
    public function testUnserialize_withReference($serializedData, $expectedData)
    {
        $unserializer = new Unserializer();
        $unserializedData = $unserializer->unserialize($serializedData);
        $this->assertEquals($expectedData, $unserializedData);
    }

    public function getSerializedDataWithReference()
    {
        $t2 = ['second array'];
        $t3 = ['third array'];
        $t1 = ['first array', &$t2, $t2, &$t2, &$t3, &$t3];

        // TODO: use objects as well as references

        return [
            $this->getSerializedDataWithExpectedUnserializedDataAsArray($t1),
        ];
    }

    /**
     * @expectedException \jvdh\Serialization\Exception\InvalidKeyException
     *
     * @dataProvider getInvalidKeyData
     *
     * @param string $serializedData
     */
    public function testUnserialize_withInvalidKeyThrowsException($serializedData)
    {
        $unserializer = new Unserializer();
        $unserializer->unserialize($serializedData);
    }

    /**
     * @return array
     */
    public function getInvalidKeyData()
    {
        return [
            ['a:1:{d:0.0;s:5:"value";}'],
            ['O:8:"stdClass":1:{d:0.0;i:1;}'],
        ];
    }
}

// TODO: Check if all flows are covered and can be simplfied
// TODO: Make another Unserializer that uses unserialize() method of php when possible (not for arrays or objects)
// TODO: Unserialize unexisting class (primary goal of this unserializer)
// TODO: Add more tests concerning references