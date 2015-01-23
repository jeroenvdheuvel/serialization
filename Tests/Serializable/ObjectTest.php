<?php
namespace jvdh\Serialization\Tests\Serializable;

use jvdh\Serialization\Serializable\Object;
use jvdh\Serialization\Serializable\PrivateObjectProperty;
use jvdh\Serialization\Serializable\ProtectedObjectProperty;
use jvdh\Serialization\Serializable\PublicObjectProperty;

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClassName()
    {
        $className = 'class name';
        $o = new Object($className);
        $this->assertSame($className, $o->getClassName());
    }

    /**
     * @dataProvider getAddPropertyData
     *
     * @param array $properties
     */
    public function testAddProperty(array $properties)
    {
        $o = new Object('class name');
        foreach ($properties as $property) {
            $o->addProperty($property);
        }

        $this->assertSame($properties, array_values($o->getDataAsArray()));
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
        $o = new Object('class name');
        $propertyName = 'p1';
        $o->addProperty(new PrivateObjectProperty($propertyName, 1));
        $o->addProperty(new ProtectedObjectProperty($propertyName, 2));
    }
}

// TODO: Make object lockable
// Make it possible to iterate