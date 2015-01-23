<?php
namespace jvdh\Serialization\Tests\Serializable;

use jvdh\Serialization\Serializable\Object as SerializableObject;
use jvdh\Serialization\Serializable\ObjectProperty;
use jvdh\Serialization\Serializable\PrivateObjectProperty;
use jvdh\Serialization\Serializable\ProtectedObjectProperty;
use jvdh\Serialization\Serializable\PublicObjectProperty;
use jvdh\Serialization\Stub\Serializable\NonexistentObjectPropertyStub;

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClassName()
    {
        $className = 'class name';
        $o = new SerializableObject($className);
        $this->assertSame($className, $o->getClassName());
    }

    /**
     * @dataProvider getAddPropertyData
     *
     * @param array|ObjectProperty[] $expectedProperties
     */
    public function testAddProperty(array $expectedProperties)
    {
        $o = $this->getObject();
        foreach ($expectedProperties as $property) {
            $o->addProperty($property);
        }

        $i = 0;

        foreach ($o as $key => $property) {
            $expectedProperty = $expectedProperties[$i];

            $this->assertSame($expectedProperty, $property);
            $this->assertSame($expectedProperty->getName(), $key);

            $i ++;
        }

        $this->assertSame(count($expectedProperties), $i);
    }

    /**
     * @dataProvider getAddPropertyData
     *
     * @param array|ObjectProperty[] $expectedProperties
     */
    public function testCount(array $expectedProperties)
    {
        $o = $this->getObject();

        foreach ($expectedProperties as $property) {
            $o->addProperty($property);
        }

        $this->assertCount(count($expectedProperties), $o);
    }

    /**
     * @return array
     */
    public function getAddPropertyData()
    {
        return [
            [[new PrivateObjectProperty('p1', 1)]],
            [[new PrivateObjectProperty('p1', 1), new ProtectedObjectProperty('p2', '2')]],
            [[new PrivateObjectProperty('p1', 1), new ProtectedObjectProperty('p2', '2'), new PublicObjectProperty('p3', [3])]],
        ];
    }

    /**
     * @expectedException \jvdh\Serialization\Exception\PropertyNameAlreadyClaimedException
     */
    public function testAddProperty_withSameNameThrowsException()
    {
        $o = $this->getObject();
        $propertyName = 'p1';
        $o->addProperty(new PrivateObjectProperty($propertyName, 1));
        $o->addProperty(new ProtectedObjectProperty($propertyName, 2));
    }

    public function testGetPropertyValue_thatExistsReturnsValue()
    {
        $o = $this->getObject();
        $p = new NonexistentObjectPropertyStub();
        $o->addProperty($p);

        $this->assertSame($p->getValue(), $o->getPropertyValueByName($p->getName()));
    }

    /**
     * @expectedException \jvdh\Serialization\Exception\PropertyNameDoesNotExistException
     */
    public function testGetPropertyValue_thatDoesNotExistsThrowsException()
    {
        $this->getObject()->getPropertyValueByName('nonexistent property');
    }

    public function testSetPropertyValue_thatExistsSetsValue()
    {
        $o = $this->getObject();
        $propertyName = 'p';
        $oldValue = 'old value';
        $newValue = ['an', 'array', 'of', 'values', 123];

        $p = new PrivateObjectProperty($propertyName, $oldValue);

        $o->addProperty($p);

        $o->setPropertyValueByName($p->getName(), $newValue);

        $this->assertSame($newValue, $o->getPropertyValueByName($p->getName()));
    }

    /**
     * @expectedException \jvdh\Serialization\Exception\PropertyNameDoesNotExistException
     */
    public function testSetPropertyValue_thatDoesNotExistsThrowsException()
    {
        $this->getObject()->setPropertyValueByName('nonexistent property', null);
    }

    /**
     * @return SerializableObject
     */
    private function getObject()
    {
        return new SerializableObject('class name');
    }
}
