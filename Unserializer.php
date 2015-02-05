<?php
namespace jvdh\Serialization;

use jvdh\Serialization\Exception\InvalidKeyException;
use jvdh\Serialization\Exception\UnsupportedSerializedVariableTypeException;
use jvdh\Serialization\Serializable\LockableObject;
use jvdh\Serialization\Serializable\ObjectProperty;
use jvdh\Serialization\Serializable\PrivateObjectProperty;
use jvdh\Serialization\Serializable\ProtectedObjectProperty;
use jvdh\Serialization\Serializable\PublicObjectProperty;

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
        $this->references = array();

        return $this->parse();
    }

    /**
     * @return array|LockableObject|bool|float|int|string|null
     * @throws UnsupportedSerializedVariableTypeException
     */
    private function &parse()
    {
        $valueType = $this->serializedData[$this->position];

        // Increase the position by 2: valueType + double dot
        $this->position += 2;

        switch ($valueType) {
            case SerializedType::TYPE_BOOLEAN:
                $this->references[] = $this->parseBoolean();
                return $this->references[count($this->references) - 1];

            case SerializedType::TYPE_NULL:
                $this->references[] = $this->parseNull();
                return $this->references[count($this->references) - 1];

            case SerializedType::TYPE_STRING:
                $this->references[] = $this->parseString();
                return $this->references[count($this->references) - 1];

            case SerializedType::TYPE_INTEGER:
                $this->references[] = $this->parseInteger();
                return $this->references[count($this->references) - 1];

            case SerializedType::TYPE_DOUBLE:
                $this->references[] = $this->parseFloat();
                return $this->references[count($this->references) - 1];

            case SerializedType::TYPE_ARRAY:
                $a = &$this->parseArray();
                return $a;

            case SerializedType::TYPE_OBJECT:
                $o = &$this->parseObject();
                return $o;

            case SerializedType::TYPE_REFERENCE:
                $r = &$this->parseReference();
                return $r;

            case strtolower(SerializedType::TYPE_REFERENCE):
                $c = $this->parseCopy();
                return $c;
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
     */
    private function &parseArray()
    {
        $int = $this->readLength();

        $result = array();
        $this->references[] = &$result;

        for ($i=0;$i<$int;$i++) {
            $key = $this->parse();

            $this->ensureKeyIsValid($key);

            // Keys can't be a reference
            array_pop($this->references);
            $value = &$this->parse();
            $result[$key] = &$value;
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
     * @return LockableObject
     */
    private function &parseObject()
    {
        $classNameLength = $this->readLength();
        $className = substr($this->serializedData, $this->position, $classNameLength);
        $this->position += $classNameLength + 2;
        $propertyLength = $this->readLength();

        $result = new LockableObject($className);
        $this->references[] = &$result;

        for ($i=0; $i<$propertyLength; $i++) {
            $key = $this->parse();
            array_pop($this->references);
            $value = &$this->parse();

            $p = $this->getSerializableObjectPropertyByRawKeyAndValue($key, $value);

            $result->addProperty($p);
        }

        $result->lock();

        $this->position += 1;

        return $result;
    }

    /**
     * @param string $rawKey
     * @param mixed $value
     * @return ObjectProperty
     */
    private function getSerializableObjectPropertyByRawKeyAndValue($rawKey, &$value)
    {
        $key = $this->getValidKeyByRawKey($rawKey);

        if (strpos($rawKey, "\0*\0") === 0) {
            return new ProtectedObjectProperty($key, $value);
        } else if (strpos($rawKey, "\0") === 0) {
            return new PrivateObjectProperty($key, $value);
        } else {
            return new PublicObjectProperty($key, $value);
        }
    }

    /**
     * @param string $key
     * @throws InvalidKeyException
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
    private function &parseReference()
    {
        $p = strpos($this->serializedData, ';', $this->position);
        $referenceIndex = substr($this->serializedData, $this->position, $p - $this->position);
        $referenceIndex = intval($referenceIndex);

        $this->position += strlen(strval($referenceIndex)) + 1;

        return $this->references[$referenceIndex - 1];
    }

    /**
     * @return mixed
     */
    private function parseCopy()
    {
        return $this->parseReference();
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
