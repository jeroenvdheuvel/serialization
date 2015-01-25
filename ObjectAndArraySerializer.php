<?php
namespace jvdh\Serialization;

use jvdh\Serialization\Exception\UnsupportedPropertyTypeException;
use jvdh\Serialization\Serializable\Object as SerializableObject;
use jvdh\Serialization\Serializable\ObjectProperty;
use jvdh\Serialization\Serializable\PrivateObjectProperty;
use jvdh\Serialization\Serializable\ProtectedObjectProperty;
use jvdh\Serialization\Serializable\PublicObjectProperty;

abstract class ObjectAndArraySerializer implements SerializerInterface
{
    /**
     * @param SerializableObject|ObjectProperty[] $data
     * @return string
     */
    protected function serializeObject(SerializableObject $data)
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
    protected function getSerializedObjectPropertyName(ObjectProperty $property, $className)
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

    /**
     * @param array $data
     * @return string
     */
    protected function serializeArray(array $data)
    {
        $arrayDataAsString = '';
        foreach ($data as $key => $value) {
            $arrayDataAsString .= $this->serialize($key);
            $arrayDataAsString .= $this->serialize($value);
        }

        return sprintf('%s:%d:{%s}', SerializedType::TYPE_ARRAY, count($data), $arrayDataAsString);
    }
}
