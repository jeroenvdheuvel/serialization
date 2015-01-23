<?php
namespace jvdh\Serialization\Tests;

use DateTime;
use DateTimeZone;
use Exception;
use jvdh\Serialization\Serializable\LockableObject;
use jvdh\Serialization\Serializable\PrivateObjectProperty;
use jvdh\Serialization\Serializable\ProtectedObjectProperty;
use jvdh\Serialization\Serializable\PublicObjectProperty;
use jvdh\Serialization\Stub\StubObjectWithPublicAndProtectedAndPrivateProperties;
use jvdh\Serialization\Unserializer;
use ReflectionObject;
use ReflectionProperty;
use stdClass;

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
    public function testUnserialize_withObject($serializedData, $expectedData)
    {
        $unserializer = new Unserializer();
        $unserializedData = $unserializer->unserialize($serializedData);

        $this->assertEquals($expectedData, $unserializedData);
    }

    /**
     * @return array
     */
    public function getSerializedObjectData()
    {
        $classWithFourProperties = new stdClass();
        $classWithFourProperties->firstProperty = 'first property';
        $classWithFourProperties->secondProperty = 123;
        $classWithFourProperties->thirdProperty = 45.6;
        $classWithFourProperties->fourthProperty = false;

        $classWithArrayAsProperty = new stdClass();
        $classWithArrayAsProperty->arrayProperty = ['array property'];

        $classWithAssociativeArrayAsProperty = new stdClass();
        $classWithAssociativeArrayAsProperty->arrayProperty = ['first key' => 'first value', 'second key' => 'second value'];

        $classWithClassAsProperty = new stdClass();
        $classWithClassAsProperty->classProperty = new stdClass();
        $classWithClassAsProperty->classProperty->anIntegerValue = 1234567890;

        $dateTime = new DateTime('2014-01-01 13:37:00', new DateTimeZone('Europe/Amsterdam'));

        return [
//            [serialize($emptyClass), $this->convertObjectToSerializableObject($emptyClass)],
//            [serialize($classWithFourProperties), $this->convertObjectToSerializableObject($classWithFourProperties)],
//            [serialize($classWithArrayAsProperty), $this->convertObjectToSerializableObject($classWithArrayAsProperty)],
//            [serialize($classWithArrayAsProperty), $this->convertObjectToSerializableObject($classWithArrayAsProperty)],
//            [serialize($classWithAssociativeArrayAsProperty), $this->convertObjectToSerializableObject($classWithAssociativeArrayAsProperty)],
//            [serialize($classWithClassAsProperty), $this->convertObjectToSerializableObject($classWithClassAsProperty)],
//            [serialize($dateTime), $this->convertObjectToSerializableObject($dateTime)],
//            [serialize($classIsSerialized), $this->convertObjectToSerializableObject($classIsSerialized)],
//            [serialize(new SerializableStubWithPublicProperties()), $this->convertObjectToSerializableObject(new SerializableStubWithPublicProperties())],
//            [serialize(new SerializableStubWithPublicAndProtectedProperties()), $this->convertObjectToSerializableObject(new SerializableStubWithPublicAndProtectedProperties())],
            [serialize(new StubObjectWithPublicAndProtectedAndPrivateProperties()), $this->convertObjectToSerializableObject(new StubObjectWithPublicAndProtectedAndPrivateProperties())],
        ];
    }

    /**
     * @param mixed $object
     * @return LockableObject
     */
    private function convertObjectToSerializableObject($object)
    {
        $reflectionObject = new ReflectionObject($object);

        $serializableObject = new LockableObject($reflectionObject->getName());

        foreach ($reflectionObject->getProperties() as $property) {
            $serializableObject->addProperty($this->getSerializableObjectPropertyByProperty($property, $object));
        }
        $serializableObject->lock();

        return $serializableObject;
    }

    /**
     * @param ReflectionProperty $property
     * @param mixed $object
     * @return PrivateObjectProperty|ProtectedObjectProperty|PublicObjectProperty
     */
    private function getSerializableObjectPropertyByProperty(ReflectionProperty $property, $object)
    {
        $property->setAccessible(true);

        $name = $property->getName();
        $value = $this->getPropertyValueByPropertyAndObject($property, $object);

        if ($property->isPublic()) {
            return new PublicObjectProperty($name, $value);
        } else if ($property->isProtected()) {
            return new ProtectedObjectProperty($name, $value);
        } else {
            return new PrivateObjectProperty($name, $value);
        }
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

    /**
     * @param ReflectionProperty $property
     * @param mixed $object
     * @return Object|mixed
     */
    private function getPropertyValueByPropertyAndObject(ReflectionProperty $property, $object)
    {
        $value = $property->getValue($object);

        if (is_object($value)) {
            $value = $this->convertObjectToSerializableObject($value);
        }

        return $value;
    }
}
// TODO: Test if it's not possible to add more properties after serialization
// TODO: Check if all flows are covered and can be simplfied
// TODO: Make another Unserializer that uses unserialize() method of php when possible (not for arrays or objects)