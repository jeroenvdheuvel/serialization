<?php
namespace jvdh\Serialization\Serializable;

class ProtectedObjectProperty extends ObjectProperty
{
    const TYPE = 1;

    /**
     * @return int
     */
    public function getType()
    {
        return self::TYPE;
    }
}