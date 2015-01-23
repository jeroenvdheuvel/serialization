<?php
namespace jvdh\Serialization;

use jvdh\Serialization\Exception\UnsupportedDataTypeException;
use jvdh\Serialization\Serializable\Object;
use jvdh\Serialization\Serializable\ObjectProperty;

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
            $arrayDataAsString = '';
            foreach ($data as $key => $value) {
                $arrayDataAsString .= $this->serialize($key);
                $arrayDataAsString .= $this->serialize($value);
            }

            return sprintf('%s:%d:{%s}', SerializedType::TYPE_ARRAY, count($data), $arrayDataAsString);
        } elseif (is_object($data) && $data instanceof Object) {
            $serializedString = '';
            $serializedString .= 'O:' . strlen($data->getClassName()) . ':"' . $data->getClassName() . '":' . count($data->getDataAsArray()) . ':{';

            foreach ($data->getDataAsArray() as $propertyName => $propertyValue) {
                $name = $this->serialize($this->getSerializedObjectPropertyName($propertyValue, $data->getClassName()));
                $value = $this->serialize($propertyValue->getValue());
                $serializedString .= $name . $value;
            }
            $serializedString .= '}';

            return $serializedString;
        }

        throw new UnsupportedDataTypeException();
    }

    private function getSerializedObjectPropertyName(ObjectProperty $property, $className)
    {
        switch ($property->getType()) {
            case SerializableObjectPropertyType::TYPE_PUBLIC:
                return $property->getName();
            case SerializableObjectPropertyType::TYPE_PROTECTED:
                return "\0*\0" . $property->getName();
            case SerializableObjectPropertyType::TYPE_PRIVATE:
                return "\0" . $className . "\0" . $property->getName();
        }

        throw new \Exception('Unsupported property type');
    }
}
