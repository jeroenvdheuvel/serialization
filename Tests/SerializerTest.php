<?php
namespace jvdh\Serialization\Tests;

use jvdh\Serialization\Serializable\Object;
use jvdh\Serialization\Serializable\PrivateObjectProperty;
use jvdh\Serialization\Serializable\ProtectedObjectProperty;
use jvdh\Serialization\Serializable\PublicObjectProperty;
use jvdh\Serialization\Serializer;
use jvdh\Serialization\Stub\Serializable\NonexistentObjectPropertyStub;
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
        $serializer = new Serializer();
        $serializedData = $serializer->serialize($unserializedData);

        $this->assertSame($expectedData, $serializedData);
    }

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
        $serializer = new Serializer();
        $serializedData = $serializer->serialize($unserializedData);

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
        $serializer = new Serializer();
        $serializedData = $serializer->serialize($unserializedData);

        $this->assertSame($expectedData, $serializedData);
    }

    public function getSerializedObjectData()
    {
//        $t = new stdClass();
//        $t->firstProperty = 'firstValue';
//        $serializedObject2 = new Object('stdClass');
//        $serializedObject2['firstProperty'] = 'firstValue';
//
//        $serializedObject3 = new Object('stdClass');
//        $serializedObject3['null'] = null;
//        $serializedObject3['integer'] = 2;
//        $serializedObject3['negativeInteger'] = -1337;
//        $serializedObject3['float'] = 13.37;
//        $serializedObject3['boolean'] = true;
//        $serializedObject3['string'] = 'a string';
//        $serializedObject3['array'] = ['first value', 'second key' => 'second value'];
//
//
//        $serializedObject4 = new Object('stdClass');
//        $serializedObject4['boolean'] = true;
//        $serializedObject4['anotherObject'] = $serializedObject2;
//        $serializedObject4['integer'] = 5;

        $serializedObject5 = new Object('jvdh\Serialization\Tests\SerializableStubWithPublicAndProtectedAndPrivateProperties');
        $serializedObject5->addProperty(new PrivateObjectProperty('privatePropertyOne', 0.1));
        $serializedObject5->addProperty(new ProtectedObjectProperty('protectedPropertyOne', 'one'));
        $serializedObject5->addProperty(new ProtectedObjectProperty('protectedPropertyTwo', null));
        $serializedObject5->addProperty(new PublicObjectProperty('publicPropertyOne', true));
        $serializedObject5->addProperty(new PublicObjectProperty('publicPropertyTwo', 12));

        return [
//            [new Object('stdClass'), 'O:8:"stdClass":0:{}'],

//            [$serializedObject2, 'O:8:"stdClass":1:{s:13:"firstProperty";s:10:"firstValue";}'],
//            [$serializedObject3, 'O:8:"stdClass":7:{s:4:"null";N;s:7:"integer";i:2;s:15:"negativeInteger";i:-1337;s:5:"float";d:13.369999999999999;s:7:"boolean";b:1;s:6:"string";s:8:"a string";s:5:"array";a:2:{i:0;s:11:"first value";s:10:"second key";s:12:"second value";}}'],
//            [$serializedObject4, 'O:8:"stdClass":3:{s:7:"boolean";b:1;s:13:"anotherObject";O:8:"stdClass":1:{s:13:"firstProperty";s:10:"firstValue";}s:7:"integer";i:5;}'],
            [$serializedObject5, "O:83:\"jvdh\\Serialization\\Tests\\SerializableStubWithPublicAndProtectedAndPrivateProperties\":5:{s:103:\"\000jvdh\\Serialization\\Tests\\SerializableStubWithPublicAndProtectedAndPrivateProperties\000privatePropertyOne\";d:0.10000000000000001;s:23:\"\000*\000protectedPropertyOne\";s:3:\"one\";s:23:\"\000*\000protectedPropertyTwo\";N;s:17:\"publicPropertyOne\";b:1;s:17:\"publicPropertyTwo\";i:12;}"],
            // TODO: Serialize with private properties
            // TODO: Seriaize with protected properties
            // TODO: Serialize with public properties
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
        $serializer = new Serializer();
        $serializer->serialize($unserializedData);
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

        $s = new Serializer();
        $s->serialize($o);
    }
}

// TODO: Check if all flows are covered and can be simplfied
// TODO: Make another Serializer that uses Serialize() method of php when possible (not for arrays or objects)