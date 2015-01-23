<?php
namespace jvdh\Serialization\Exception;

class UnsupportedSerializedVariableTypeException extends SerializationException
{
    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $message = sprintf('Type [%s] is an unsupported type', $type);
        parent::__construct($message);
    }
}
