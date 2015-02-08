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
     * @var array
     */
    private $references;

    /**
     * @inheritdoc
     */
    public function serialize($data)
    {
        $this->references = array();

        return $this->parse($data);
    }

    /**
     * @param mixed $data
     * @return string
     * @throws UnsupportedDataTypeException
     */
    protected function parse(&$data)
    {
        try {
            return sprintf('%s:%s;', SerializedType::TYPE_REFERENCE_VARIABLE, $this->getReferenceNumberOrThrowException($data));
        } catch (\Exception $e) {

        }

        try {
            return sprintf('%s:%s;', SerializedType::TYPE_POINTING_TO_SAME_OBJECT, $this->getCopyNumberOrThrowException($data));
        } catch (\Exception $e) {

        }

        $this->references[] = &$data;

        if ($data === null || is_bool($data) || is_int($data) || is_float($data) || is_string($data)) {
            return serialize($data);
        } elseif (is_array($data)) {
            return $this->serializeArray($data);
        } elseif ($data instanceof SerializableObject) {
            return $this->serializeObject($data);
        }

        throw new UnsupportedDataTypeException();
    }

    /**
     * @param SerializableObject|ObjectProperty[] $data
     * @return string
     */
    protected function serializeObject(SerializableObject &$data)
    {
        $serializedString = 'O:' . strlen($data->getClassName()) . ':"' . $data->getClassName() . '":' . count($data) . ':{';

        foreach ($data as $objectProperty) {
            $properyName = $this->getSerializedObjectPropertyName($objectProperty, $data->getClassName());
            $name = $this->parse($properyName);
            // Keys can't be a reference
            array_pop($this->references);

            $propertyValue = &$objectProperty->getValue();
            $value = $this->parse($propertyValue);

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
        foreach ($data as $key => &$value) {
            $arrayDataAsString .= $this->parse($key);
            // Keys can't be a reference
            array_pop($this->references);

            $arrayDataAsString .= $this->parse($value);
        }

        return sprintf('%s:%d:{%s}', SerializedType::TYPE_ARRAY, count($data), $arrayDataAsString);
    }

    protected function getReferenceNumberOrThrowException(&$var)
    {
        foreach ($this->references as $i => &$reference) {
            if ($this->isReference($var, $reference)) {
                return $i + 1;
            }
        }

        throw new \Exception('Not a reference');
    }

    /**
     * @param mixed $var1
     * @param mixed $var2
     * @return bool
     */
    protected function isReference(&$var1, &$var2)
    {
        $same = false;
        if ($var1 === $var2) {
            $originalVar1 = $var1;
            do {
                $newVar1 = uniqid();
            } while ($var1 === $newVar1);

            $var1 = $newVar1;

            if ($var2 === $newVar1) {
                $same = true;
            }
            $var1 = $originalVar1;
        }
        return $same;
    }

    private function getCopyNumberOrThrowException($data)
    {
        if ($data instanceof SerializableObject) {
            foreach ($this->references as $i => $reference) {
                if ($data === $reference) {
                    return $i + 1;
                }
            }
        }

        throw new \Exception('Not a copy');
    }
}
