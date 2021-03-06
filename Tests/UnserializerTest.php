<?php
namespace jvdh\Serialization\Tests;

use jvdh\Serialization\Serializable\LockableObject;
use jvdh\Serialization\Serializable\Object as SerializableObject;
use jvdh\Serialization\Serializable\ObjectProperty;
use jvdh\Serialization\Stub\Serializable\ArrayLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\ArrayStub;
use jvdh\Serialization\Stub\Serializable\EmptyLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\EmptyStub;
use jvdh\Serialization\Stub\Serializable\ObjectContainingAnotherObjectLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\ObjectContainingAnotherObject;
use jvdh\Serialization\Stub\Serializable\ObjectContainingObjectReferencesLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\ObjectContainingObjectReferencesStub;
use jvdh\Serialization\Stub\Serializable\ObjectContainingSimpleReferencesLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\ObjectContainingSimpleReferencesStub;
use jvdh\Serialization\Stub\Serializable\SimpleLockableObjectStub;
use jvdh\Serialization\Stub\Serializable\SimpleStub;
use jvdh\Serialization\Unserializer;

class UnserializerTest extends AbstractSerializableTest
{
    /**
     * @dataProvider getSerializedSimpleData
     *
     * @param string $serializedData
     * @param string $expectedData
     */
    public function testUnserialize_withSimpleData($serializedData, $expectedData)
    {
        $unserializedData = $this->getUnserializer()->unserialize($serializedData);

        $this->assertSame($expectedData, $unserializedData);
    }

    /**
     * @return array
     */
    public function getSerializedSimpleData()
    {
        return array(
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(true),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(null),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray('true'),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray('0123456789'),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(123),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(1.23),
        );
    }

    /**
     * @dataProvider getSerializedArrayData
     *
     * @param mixed$serializedData
     * @param string $expectedData
     */
    public function testUnserialize_withArray($serializedData, $expectedData)
    {
        $unserializedData = $this->getUnserializer()->unserialize($serializedData);

        $this->assertEquals($expectedData, $unserializedData);
    }

    public function getSerializedArrayData()
    {
        return array(
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(array()),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(array('0123456789', 1)),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(array(2, 4, 1234)),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(array(123, 456)),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(array(123, 456, '789')),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(array('abc' => 123)),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(array('abc' => 'qwe')),
            $this->getSerializedDataWithExpectedUnserializedDataAsArray(array('abc' => 'qwe', 4 => 123, 5 => 'aab')),
        );
    }

    /**
     * @dataProvider getSerializedObjectData
     *
     * @param string $serializedData
     * @param mixed $expectedData
     */
    public function testUnserialize_withObject($serializedData, LockableObject $expectedData)
    {
        $unserializedData = $this->getUnserializer()->unserialize($serializedData);

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
        $properties = array();

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
        return array(
            array(serialize(new EmptyStub()), new EmptyLockableObjectStub()),
            array(serialize(new SimpleStub()), new SimpleLockableObjectStub()),
            array(serialize(new ArrayStub()), new ArrayLockableObjectStub()),
            array(serialize(new ObjectContainingAnotherObject()), new ObjectContainingAnotherObjectLockableObjectStub()),
            array(serialize(new ObjectContainingSimpleReferencesStub()), new ObjectContainingSimpleReferencesLockableObjectStub()),
            array(serialize(new ObjectContainingObjectReferencesStub()), new ObjectContainingObjectReferencesLockableObjectStub()),
        );
    }

    /**
     * @param mixed $data
     * @return array
     */
    private function getSerializedDataWithExpectedUnserializedDataAsArray($data)
    {
        return array(serialize($data), $data);
    }

    /**
     * @expectedException \jvdh\Serialization\Exception\UnsupportedSerializedVariableTypeException
     */
    public function testUnserialize_withUnknowntypeThrowsException()
    {
        $this->getUnserializer()->unserialize('Q:123');
    }

    /**
     * @dataProvider getSerializedDataWithReference
     *
     * @param string $serializedData
     */
    public function testUnserialize_withReference($serializedData, $expectedData)
    {
        $unserializedData = $this->getUnserializer()->unserialize($serializedData);
        $this->assertEquals($expectedData, $unserializedData);
    }

    public function getSerializedDataWithReference()
    {
        $t2 = array('second array');
        $t3 = array('third array');
        $t1 = array('first array', &$t2, $t2, &$t2, &$t3, &$t3);

        return array(
            $this->getSerializedDataWithExpectedUnserializedDataAsArray($t1),
        );
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
        $this->getUnserializer()->unserialize($serializedData);
    }

    /**
     * @return array
     */
    public function getInvalidKeyData()
    {
        return array(
            array('a:1:{d:0.0;s:5:"value";}'),
            array('O:8:"stdClass":1:{d:0.0;i:1;}'),
        );
    }

    public function testUnserialize_withArrayContainingReferences()
    {
        $arrayWithReferences = array();
        $arrayWithReferences['firstValue'] = 123;
        $arrayWithReferences['firstValueAndNotReference'] = $arrayWithReferences['firstValue'];
        $arrayWithReferences['firstValueAndReference'] = &$arrayWithReferences['firstValue'];
        $arrayWithReferences['secondValue'] = 'abc';
        $arrayWithReferences['thirdValue'] = false;
        $arrayWithReferences['thirdValueAndReference'] = &$arrayWithReferences['thirdValue'];
        $arrayWithReferences['fourthValue'] = array('firstValue' => &$arrayWithReferences['firstValue']);
        $arrayWithReferences['fifthValue'] = array();
        $arrayWithReferences['fifthValueAndReference'] = &$arrayWithReferences['fifthValue'];

        $serializedArrayWithReferences = serialize($arrayWithReferences);

        $data = $this->getUnserializer()->unserialize($serializedArrayWithReferences);

        // Change References values on both arrays
        $arrayWithReferences['firstValue'] = $data['firstValue'] = 'abc';
        $arrayWithReferences['thirdValue'] = $data['thirdValue'] = 1;
        $arrayWithReferences['fifthValueAndReference'] = $data['fifthValueAndReference'] = false;

        $this->assertSame($arrayWithReferences, $data);
    }

    public function testUnserialize_thatObjectAndReferenceAreSame()
    {
        $serializedData = serialize(new ObjectContainingSimpleReferencesStub());

        $data = $this->getUnserializer()->unserialize($serializedData);

        $firstValue = 5;
        $secondValue = 'a';
        $data->setPropertyValueByName('publicFirstValue', $firstValue);
        $data->setPropertyValueByName('publicSecondValue', $secondValue);

        $this->assertSame($firstValue, $data->getPropertyValueByName('publicThirdValue'));
        $this->assertSame($secondValue, $data->getPropertyValueByName('publicFifthValue'));
    }

    public function testUnserialize_withArrayContainingObjectsAsReferencesAreSame()
    {
        $emptyStub = new EmptyStub();
        $serializedData = serialize(array(&$emptyStub, &$emptyStub));

        $this->ensureNativeSerializeIsAbleToReturnReferencesToObjectsOrSkip();

        $data = $this->getUnserializer()->unserialize($serializedData);

        $this->assertSame($data[0], $data[1]);
        $data[0] = null;
        $this->assertSame($data[0], $data[1]);
    }

    public function testUnserialize_withArrayContainingObjectsAsCopiesAreNotSameAfterChanging()
    {
        $emptyStub = new EmptyStub();
        $serializedData = serialize(array($emptyStub, $emptyStub));

        $data = $this->getUnserializer()->unserialize($serializedData);

        $this->assertSame($data[0], $data[1]);
        $data[0] = null;
        $this->assertNotSame($data[0], $data[1]);
    }

    public function testUnserialize_withNonExistingClass()
    {
        $className = 'NonExistingClass';
        $serializedData = sprintf('O:%d:"%s":0:{}', strlen($className), $className);

        $data = $this->getUnserializer()->unserialize($serializedData);
        $this->assertSame($className, $data->getClassName());
        $this->assertCount(0, $data);
    }

    /**
     * @return Unserializer
     */
    private function getUnserializer()
    {
        return new Unserializer();
    }
}
