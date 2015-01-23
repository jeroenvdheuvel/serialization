<?php
namespace jvdh\Serialization\Serializable;

class PrivateObjectProperty extends ObjectProperty
{
    const TYPE = 0;

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return self::TYPE;
    }
}