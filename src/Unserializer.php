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

    public function unserialize()
    {
        $valueType = $this->serializedData[$this->position];
        $this->position += 2;

        // TODO: Increase index by 2, because all types are only one character + double dot

        switch ($valueType) {
            case 'b':
                $result = $this->unserializeBoolean();
                $this->references[] = $result;
                break;

            case 'N':
                $result = $this->unserializeNull();
                $this->references[] = $result;
                break;

            case 's':
                $result = $this->unserializeString();
                $this->references[] = $result;
                break;

            case 'i':
                $result = $this->unserializeInteger();
                $this->references[] = $result;
                break;

            case 'd':
                $result = $this->unserializeDouble();
                $this->references[] = $result;
                break;

            case 'a':
                $result = $this->unserializeArray();
                break;

            case 'O':
                $result = $this->unserializeObject();
                break;

//            case 'r':
            case 'R':
                $result = $this->unserializeReference();
                break;

            default:
                throw new Exception(sprintf('Type [%s] is an unsupported type', $valueType));
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function unserializeBoolean()
    {
        $result = $this->serializedData[$this->position] === '1';

        $this->position += 2;

        return $result;
    }

    private function unserializeNull()
    {
        return null;
    }

    private function unserializeString()
    {
        $endPosition = strpos($this->serializedData, ':', $this->position);
        $stringLength = intval(substr($this->serializedData, $this->position, $endPosition - $this->position));
        $stringLengthCharacterCount = strlen(strval($stringLength));

        $result = substr($this->serializedData, $this->position + $stringLengthCharacterCount + 2, $stringLength);
        $this->position +=  $stringLengthCharacterCount + 4 + $stringLength;

        return $result;
    }

    private function unserializeInteger()
    {
        return intval($this->unserializeNumberAsString());
    }

    private function unserializeDouble()
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

    private function unserializeArray()
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

    private function unserializeObject()
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

    private function unserializeReference()
    {
        // TODO: Really return references when

        $p = strpos($this->serializedData, ';', $this->position);
        $referenceIndex = substr($this->serializedData, $this->position, $p - $this->position);
        $referenceIndex = intval($referenceIndex);

        $this->position += strlen(strval($referenceIndex)) + 1;

        return $this->references[$referenceIndex - 1];
    }
}
