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
        $unserializer = new Unserializer($serializedData);
        $unserializedData = $unserializer->unserialize();

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
        $unserializer = new Unserializer($serializedData);
        $unserializedData = $unserializer->unserialize();

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
        $unserializer = new Unserializer($serializedData);
        $unserializedData = $unserializer->unserialize();

//        var_dump($unserializedData);
//        $this->assertEquals($expectedData, $unserializedData);
    }

    public function getSerializedObjectData()
    {
        $d = new stdClass();
        $d->a = 1;
        $d->b = '0123456789';
        $d->c = true;
        $d->d = 0.1;
        $d->e = [1, 'b' => 0.5, 'c' => true];
        $d->f = 0.1;
        $d->g = 'one';
        $d->h = new stdClass();
        $d->h->aa = 2;
        $d->h->bb = '1234567890';
        $d->k = 23;
        $d->h->cc = 'aas';
        $d->m = 0.5;
        $d->h->cc = [2 => 1, 'a' => 'b', 'c' => true, 4];

        return [
            $this->getSerializedDataWithExpectedUnserializedDataAsArray($d),
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
        $unserializer = new Unserializer('Q:123');
        $unserializer->unserialize();
    }

    /**
     * @dataProvider getSerializedDataWithReference
     *
     * @param string $serializedData
     * @throws Exception
     */
    public function testUnserialize_withReference($serializedData, $expectedData)
    {
        $unserializer = new Unserializer($serializedData);
        $unserializedData = $unserializer->unserialize();
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