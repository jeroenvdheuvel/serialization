<?php
namespace jvdh\Serialization\Tests\Serializable;

use jvdh\Serialization\Serializable\ObjectProperty;

class NonexistentObjectPropertyStub extends ObjectProperty
{
    public function __construct()
    {

    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return -1;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'name';
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return 'value';
    }
}
