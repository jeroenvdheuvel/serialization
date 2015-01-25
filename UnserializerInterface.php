<?php
namespace jvdh\Serialization;

use jvdh\Serialization\Exception\UnsupportedSerializedVariableTypeException;
use jvdh\Serialization\Serializable\Object as SerializableObject;

interface UnserializerInterface
{
    /**
     * @param mixed $data
     * @return array|SerializableObject|bool|float|int|string|null
     * @throws UnsupportedSerializedVariableTypeException
     */
    public function unserialize($data);
}