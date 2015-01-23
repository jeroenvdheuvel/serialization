<?php
namespace jvdh\Serialization\Tests\Serializable;

use jvdh\Serialization\Serializable\LockableObject;

class LockableObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testIsLocked_returnsTrue()
    {
        $o = $this->getObject();
        $this->assertFalse($o->isLocked());
    }

    public function testIsLocked_thatIsLockedReturnsFalse()
    {
        $o = $this->getObject();
        $o->lock();
        $this->assertTrue($o->isLocked());
    }

    public function testIsLocked_thatIsLockedMultipleTimesReturnsFalse()
    {
        $o = $this->getObject();
        $o->lock();
        $o->lock();
        $this->assertTrue($o->isLocked());
    }

    /**
     * @return LockableObject
     */
    private function getObject()
    {
        return new LockableObject('class name');
    }

    public function testAddProperty_onUnlockedObjectSucceeds()
    {
        $o = $this->getObject();
        $o->addProperty(new NonexistentObjectPropertyStub());
    }

    /**
     * @expectedException \jvdh\Serialization\Exception\ObjectIsLockedException
     */
    public function testAddProperty_onLockedObjectThrowsException()
    {
        $o = $this->getObject();
        $o->lock();
        $o->addProperty(new NonexistentObjectPropertyStub());
    }
}
 