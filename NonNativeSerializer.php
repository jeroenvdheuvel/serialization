<?php
namespace jvdh\Serialization;

use jvdh\Serialization\Exception\UnsupportedDataTypeException;
use jvdh\Serialization\Serializable\Object as SerializableObject;

class NonNativeSerializer extends ObjectAndArraySerializer
{
    /**
     * @inheritdoc
     */
    public function serialize($data)
    {
        if ($data === null) {
            return SerializedType::TYPE_NULL. ';';
        } elseif (is_bool($data)) {
            return sprintf('%s:%d;', SerializedType::TYPE_BOOLEAN, intval($data));
        } elseif (is_int($data)) {
            return sprintf('%s:%d;', SerializedType::TYPE_INTEGER, $data);
        } elseif (is_float($data)) {
            return sprintf('%s:%s;', SerializedType::TYPE_DOUBLE, var_export($data, true));
        } elseif (is_string($data)) {
            return sprintf('%s:%d:"%s";', SerializedType::TYPE_STRING, strlen($data), $data);
        } elseif (is_array($data)) {
            return $this->serializeArray($data);
        } elseif (is_object($data) && $data instanceof SerializableObject) {
            return $this->serializeObject($data);
        }

        throw new UnsupportedDataTypeException();
    }
}
