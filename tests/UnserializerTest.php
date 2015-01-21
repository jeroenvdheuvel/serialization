<?php
namespace jvdh\Serialization\Tests;

use DateTime;
use DateTimeZone;
use Exception;
use jvdh\Serialization\SerializableObject;
use jvdh\Serialization\TestClassThatCanBeSerializedStub;
use jvdh\Serialization\Unserializer;
use ReflectionObject;
use stdClass;

class UnserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSerializedSimpleData
     *
     * @param string $serializedData
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
//        var_dump($expectedData, $unserializedData);
//        var_dump($serializedData);
//        exit;
//        $data = unserialize($serializedData);
//        var_dump($data);
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
        $classIsSerialized = new TestClassThatCanBeSerializedStub($dateTime);

        return [
//            [serialize($emptyClass), $this->convertObjectToSerializableObject($emptyClass)],
//            [serialize($classWithFourProperties), $this->convertObjectToSerializableObject($classWithFourProperties)],
//            [serialize($classWithArrayAsProperty), $this->convertObjectToSerializableObject($classWithArrayAsProperty)],
//            [serialize($classWithArrayAsProperty), $this->convertObjectToSerializableObject($classWithArrayAsProperty)],
//            [serialize($classWithAssociativeArrayAsProperty), $this->convertObjectToSerializableObject($classWithAssociativeArrayAsProperty)],
//            [serialize($classWithClassAsProperty), $this->convertObjectToSerializableObject($classWithClassAsProperty)],
//            [serialize($dateTime), $this->convertObjectToSerializableObject($dateTime)],
            [serialize($classIsSerialized), $this->convertObjectToSerializableObject($classIsSerialized)],
        ];
    }

    private function convertObjectToSerializableObject($object)
    {
        $reflectionObject = new ReflectionObject($object);

        $serializableObject = new SerializableObject($reflectionObject->getName());

        foreach ($reflectionObject->getProperties() as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $propertyValue = $property->getValue($object);

            if (is_object($propertyValue)) {
                $propertyValue = $this->convertObjectToSerializableObject($propertyValue);
            }

            $serializableObject[$propertyName] = $propertyValue;
        }

        return $serializableObject;
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
     * @expectedException \Exception
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
     * @expectedException \RuntimeException
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
// TODO: Use magic methods