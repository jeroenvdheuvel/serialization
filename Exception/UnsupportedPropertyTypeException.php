<?php
namespace jvdh\Serialization\Exception;

class UnsupportedPropertyTypeException extends SerializationException
{
    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $message = sprintf('Property type [%s] is unsupported');
        parent::__construct($message);
    }
}
