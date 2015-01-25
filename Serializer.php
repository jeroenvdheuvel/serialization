<?php
namespace jvdh\Serialization;

use jvdh\Serialization\Exception\UnsupportedDataTypeException;
use jvdh\Serialization\Exception\UnsupportedPropertyTypeException;
use jvdh\Serialization\Serializable\Object as SerializableObject;
use jvdh\Serialization\Serializable\ObjectProperty;
use jvdh\Serialization\Serializable\PrivateObjectProperty;
use jvdh\Serialization\Serializable\ProtectedObjectProperty;
use jvdh\Serialization\Serializable\PublicObjectProperty;

class Serializer implements SerializerInterface
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

    /**
     * @param array $data
     * @return string
     */
    private function serializeArray(array $data)
    {
        $arrayDataAsString = '';
        foreach ($data as $key => $value) {
            $arrayDataAsString .= $this->serialize($key);
            $arrayDataAsString .= $this->serialize($value);
        }

        return sprintf('%s:%d:{%s}', SerializedType::TYPE_ARRAY, count($data), $arrayDataAsString);
    }

    /**
     * @param SerializableObject|ObjectProperty[] $data
     * @return string
     */
    private function serializeObject(SerializableObject $data)
    {
        $serializedString = 'O:' . strlen($data->getClassName()) . ':"' . $data->getClassName() . '":' . count($data) . ':{';

        foreach ($data as $propertyValue) {
            $name = $this->serialize($this->getSerializedObjectPropertyName($propertyValue, $data->getClassName()));
            $value = $this->serialize($propertyValue->getValue());
            $serializedString .= $name . $value;
        }
        $serializedString .= '}';

        return $serializedString;
    }

    /**
     * @param ObjectProperty $property
     * @param string $className
     * @return string
     * @throws UnsupportedPropertyTypeException
     */
    private function getSerializedObjectPropertyName(ObjectProperty $property, $className)
    {
        switch ($property->getType()) {
            case PublicObjectProperty::TYPE:
                return $property->getName();

            case ProtectedObjectProperty::TYPE:
                return "\0*\0" . $property->getName();

            case PrivateObjectProperty::TYPE:
                return "\0" . $className . "\0" . $property->getName();
        }

        throw new UnsupportedPropertyTypeException($property->getType());
    }
}
