<?php
namespace jvdh\Serialization\Serializable;

class ProtectedObjectProperty extends ObjectProperty
{
    const TYPE = 1;

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return self::TYPE;
    }
}