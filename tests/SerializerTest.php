<?php
namespace jvdh\Serialization;

class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getUnserializedPrimitiveData
     *
     * @param mixed $unserializedData
     * @param string $expectedData
     */
    public function testSerialize_withPrimitives($unserializedData, $expectedData)
    {
        $serializer = new Serializer();
        $serializedData = $serializer->serialize($unserializedData);

//        var_dump($expectedData);
//        var_dump(floatval($unserializedData));
        $this->assertSame($expectedData, $serializedData);
    }

    public function getUnserializedPrimitiveData()
    {
        return [
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(null),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(123),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(-456),
            $this->getUnserializedDataWithExpectedSerializedDataAsArray(7.89), // TODO: Make sure a fixed precision
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
        $this->markTestIncomplete();
        $serializer = new Serializer();
        $serializedData = $serializer->serialize($unserializedData);

        $this->assertSame($expectedData, $serializedData);
    }

    public function getSerializedObjectData()
    {
        return [
            [new SerializableObject('stdClass'), 'O:8:"stdClass":0:{}'],
            // TODO: Serialize with private properties
            // TODO: Seriaize with protected properties
            // TODO: Serialize with public properties
            // TODO: Serialize with different types: boolean, integer, double, null, array and another object
        ];
    }

    /**
     * @dataProvider getSerializedUnsupportedData
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Unsupported data type
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
        ];
    }
}
