<?php
namespace jvdh\Serialization\Serializable;

class PublicObjectProperty extends ObjectProperty
{
    const TYPE = 2;

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return self::TYPE;
    }
}