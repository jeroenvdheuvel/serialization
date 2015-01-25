<?php
namespace jvdh\Serialization;

use jvdh\Serialization\Exception\UnsupportedDataTypeException;
use jvdh\Serialization\Serializable\Object as SerializableObject;

class PartiallyNativeSerializer extends ObjectAndArraySerializer
{
    /**
     * @inheritdoc
     */
    public function serialize($data)
    {
        if ($data === null || is_bool($data) || is_int($data) || is_float($data) || is_string($data)) {
            return serialize($data);
        } elseif (is_array($data)) {
            return $this->serializeArray($data);
        } elseif (is_object($data) && $data instanceof SerializableObject) {
            return $this->serializeObject($data);
        }

        throw new UnsupportedDataTypeException();
    }
}
