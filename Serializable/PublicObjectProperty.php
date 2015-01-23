<?php
namespace jvdh\Serialization\Serializable;

class PublicObjectProperty extends ObjectProperty
{
    const TYPE = 2;

    /**
     * @return int
     */
    public function getType()
    {
        return self::TYPE;
    }
}