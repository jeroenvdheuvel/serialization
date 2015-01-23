<?php
namespace jvdh\Serialization;

use Exception;
use jvdh\Serialization\Exception\InvalidKeyException;
use jvdh\Serialization\Exception\UnsupportedSerializedVariableTypeException;
use jvdh\Serialization\Serializable\Object;
use jvdh\Serialization\Serializable\SerializableObjectProperty;

class Unserializer implements UnserializerInterface
{
    /**
     * @var int
     */
    private $position;

    /**
     * @var string
     */
    private $serializedData;

    /**
     * @var array
     */
    private $references;

    /**
     * @inheritdoc
     */
    public function unserialize($data)
    {
        $this->position = 0;
        $this->serializedData = $data;
        $this->references = [];

        return $this->parse();
    }

    /**
     * @return array|bool|float|int|null|string
     * @throws Exception
     */
    private function parse()
    {
        $valueType = $this->serializedData[$this->position];

        // Increase the position by 2: valueType + double dot
        $this->position += 2;

        switch ($valueType) {
            case SerializedType::TYPE_BOOLEAN:
                return $this->references[] = $this->parseBoolean();

            case SerializedType::TYPE_NULL:
                return $this->references[] = $this->parseNull();

            case SerializedType::TYPE_STRING:
                return $this->references[] = $this->parseString();

            case SerializedType::TYPE_INTEGER:
                return $this->references[] = $this->parseInteger();

            case SerializedType::TYPE_DOUBLE:
                return $this->references[] = $this->parseFloat();

            case SerializedType::TYPE_ARRAY:
                return $this->parseArray();

            case SerializedType::TYPE_OBJECT:
                return $this->parseObject();

//            case 'r':
            case SerializedType::TYPE_REFERENCE:
                return $this->parseReference();
        }

        throw new UnsupportedSerializedVariableTypeException($valueType);
    }

    /**
     * @return bool
     */
    private function parseBoolean()
    {
        $result = $this->serializedData[$this->position] === '1';
        $this->position += 2;

        return $result;
    }

    /**
     * @return null
     */
    private function parseNull()
    {
        return null;
    }

    /**
     * @return string
     */
    private function parseString()
    {
        $length = $this->readLength();
        $result = substr($this->serializedData, $this->position, $length);
        $this->position +=  2 + $length;

        return $result;
    }

    /**
     * @return int
     */
    private function parseInteger()
    {
        return intval($this->parseFloat());
    }

    /**
     * @return float
     */
    private function parseFloat()
    {
        $endPosition = strpos($this->serializedData, ';', $this->position);
        $result = substr($this->serializedData, $this->position, $endPosition - $this->position);
        $this->position = $endPosition + 1;

        return floatval($result);
    }

    /**
     * @return array
     * @throws Exception
     */
    private function parseArray()
    {
        $int = $this->readLength();

        $result = [];
        $this->references[] = &$result;

        for ($i=0;$i<$int;$i++) {
            $key = $this->parse();

            $this->ensureKeyIsValid($key);

            // Keys can't be a reference
            array_pop($this->references);
            $value = $this->parse();
            $result[$key] = $value;
        }

        $this->position += 1;

        return $result;
    }

    /**
     * @return int
     */
    private function readLength()
    {
        $delimiter = strpos($this->serializedData, ':', $this->position);
        $length = substr($this->serializedData, $this->position, $delimiter - $this->position);

        $this->position = $delimiter + 2;

        return intval($length);
    }

    /**
     * @return array
     * @throws Exception
     */
    private function parseObject()
    {
        $classNameLength = $this->readLength();
        $className = substr($this->serializedData, $this->position, $classNameLength);
        $this->position += $classNameLength + 2;
        $propertyLength = $this->readLength();

        $result = new Object($className);
        $this->references[] = &$result;

        for ($i=0; $i<$propertyLength; $i++) {
            $key = $this->parse();
            array_pop($this->references);
            $value = $this->parse();

            $p = $this->getSerializableObjectPropertyByRawKeyAndValue($key, $value);

            $result[$p->getName()] = $p;
        }

        $this->position += 1;

        return $result;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return SerializableObjectProperty
     */
    private function getSerializableObjectPropertyByRawKeyAndValue($key, $value)
    {
        $propertyType = $this->getPropertyTypeByKey($key);

        $key = $this->getValidKeyByRawKey($key);

        return new SerializableObjectProperty($propertyType, $key, $value);
    }

    /**
     * @param string $key
     */
    private function ensureKeyIsValid($key)
    {
        if (!is_string($key) && !is_int($key)) {
            throw new InvalidKeyException();
        }
    }

    /**
     * @return mixed
     */
    private function parseReference()
    {
        // TODO: Really return references when

        $p = strpos($this->serializedData, ';', $this->position);
        $referenceIndex = substr($this->serializedData, $this->position, $p - $this->position);
        $referenceIndex = intval($referenceIndex);

        $this->position += strlen(strval($referenceIndex)) + 1;

        return $this->references[$referenceIndex - 1];
    }

    /**
     * @param string $key
     * @return int
     */
    private function getPropertyTypeByKey($key)
    {
        if (strpos($key, "\0*\0") === 0) {
            return SerializableObjectPropertyType::TYPE_PROTECTED;
        } else if (strpos($key, "\0") === 0) {
            return SerializableObjectPropertyType::TYPE_PRIVATE;
        } else {
            return SerializableObjectPropertyType::TYPE_PUBLIC;
        }
    }

    /**
     * @param string $key
     * @return string
     */
    private function getValidKeyByRawKey($key)
    {
        $p = strrpos($key, "\0");
        if ($p !== false) {
            $key = substr($key, $p + 1);
            return $key;
        }

        $this->ensureKeyIsValid($key);

        return $key;
    }
}


// FACTS: