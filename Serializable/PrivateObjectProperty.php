<?php
namespace jvdh\Serialization\Serializable;

class PrivateObjectProperty extends ObjectProperty
{
    const TYPE = 0;

    /**
     * @return int
     */
    public function getType()
    {
        return self::TYPE;
    }
}