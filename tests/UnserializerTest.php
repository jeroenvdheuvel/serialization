<?php
namespace jvdh\Serialization;

use Exception;
use stdClass;

class UnserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSerializedPrimitiveData
     *
     * @param string $serializedData
     */
    public function testUnserialize_withPrimitives($serializedData, $expectedData)
    {
        $unserializer = new Unserializer();
        $unserializedData = $unserializer->unserialize($serializedData);

        $this->assertSame($expectedData, $unserializedData);
    }

    // TODO: String not not really a primitive

    /**
     * @return array
     */
    public function getSerializedPrimitiveData()
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
     * @dataProvider getSerializedNonPrimitiveData
     *
     * @param mixed$serializedData
     * @param string $expectedData
     * @throws \Exception
     */
    public function testUnserialize_withNonPrimitives($serializedData, $expectedData)
    {
        $unserializer = new Unserializer();
        $unserializedData = $unserializer->unserialize($serializedData);

        $this->assertEquals($expectedData, $unserializedData);
    }

    public function getSerializedNonPrimitiveData()
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

        $this->assertSame($expectedData, $unserializedData);
//        $this->assertSame(array_keys($expectedData, $expectedData))
    }

    public function getSerializedObjectData()
    {
        $emptyClass = new stdClass();

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
        return [
            [serialize($emptyClass), []],
            [serialize($classWithFourProperties), ['firstProperty' => 'first property', 'secondProperty' => 123, 'thirdProperty' => 45.6, 'fourthProperty' => false]],
            [serialize($classWithArrayAsProperty), ['arrayProperty' => ['array property']]],
            [serialize($classWithAssociativeArrayAsProperty), ['arrayProperty' => ['first key' => 'first value', 'second key' => 'second value']]],
            [serialize($classWithClassAsProperty), ['classProperty' => ['anIntegerValue' => 1234567890]]],
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

        return [
            $this->getSerializedDataWithExpectedUnserializedDataAsArray($t1),

        ];
    }


}
// TODO: Use magic methods