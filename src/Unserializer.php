<?php
namespace jvdh\Serialization;

use Exception;

class Unserializer
{
    private $position;
    private $serializedData;
    private $references;

    public function __construct($serializedData)
    {
        $this->position = 0;
        $this->serializedData = $serializedData;
        $this->references = [];
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function unserialize()
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
                return $this->references[] = $this->parseDouble();

            case SerializedType::TYPE_ARRAY:
                return $this->parseArray();

            case SerializedType::TYPE_OBJECT:
                return $this->parseObject();

//            case 'r':
            case SerializedType::TYPE_REFERENCE:
                return $this->parseReference();

            default:
                throw new Exception(sprintf('Type [%s] is an unsupported type', $valueType));
        }
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

    private function parseNull()
    {
        return null;
    }

    private function parseString()
    {
        $endPosition = strpos($this->serializedData, ':', $this->position);
        $stringLength = intval(substr($this->serializedData, $this->position, $endPosition - $this->position));
        $stringLengthCharacterCount = strlen(strval($stringLength));

        $result = substr($this->serializedData, $this->position + $stringLengthCharacterCount + 2, $stringLength);
        $this->position +=  $stringLengthCharacterCount + 4 + $stringLength;

        return $result;
    }

    private function parseInteger()
    {
        return intval($this->unserializeNumberAsString());
    }

    private function parseDouble()
    {
        return doubleval($this->unserializeNumberAsString());
    }

    /**
     * @return string
     */
    private function unserializeNumberAsString()
    {
        $endPosition = strpos($this->serializedData, ';', $this->position);
        $result = substr($this->serializedData, $this->position, $endPosition - $this->position);
        $this->position = $endPosition + 1;

        return $result;
    }

    private function parseArray()
    {
        $int = $this->readLength();

        $result = [];
        $this->references[] = &$result;

        for ($i=0;$i<$int;$i++) {
            $key = $this->unserialize();

            // Keys can't be a reference
            array_pop($this->references);
            $value = $this->unserialize();
            $result[$key] = $value;
        }

        $this->position += 1;

        return $result;
    }

    private function readLength()
    {
        $delimiter = strpos($this->serializedData, ':', $this->position);
        $length = substr($this->serializedData, $this->position, $delimiter - $this->position);

        $this->position = $delimiter + 2;

        return intval($length);
    }

    // TODO: Rename unserialize to parse

    private function parseObject()
    {
        $classNameLength = $this->readLength();
        $className = substr($this->serializedData, $this->position, $classNameLength);
        $this->position += $classNameLength + 2;
        $propertyLength = $this->readLength();

        $result = [];
        $this->references[] = &$result;

        for ($i=0;$i<$propertyLength;$i++) {
            $key = $this->unserialize();
            array_pop($this->references);
            $value = $this->unserialize();
            $result[$key] = $value;
        }

        $this->position += 1;

        return $result;
    }

    private function parseReference()
    {
        // TODO: Really return references when

        $p = strpos($this->serializedData, ';', $this->position);
        $referenceIndex = substr($this->serializedData, $this->position, $p - $this->position);
        $referenceIndex = intval($referenceIndex);

        $this->position += strlen(strval($referenceIndex)) + 1;

        return $this->references[$referenceIndex - 1];
    }
}
