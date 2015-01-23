<?php
namespace jvdh\Serialization\Exception;

class PropertyNameDoesNotExistException extends SerializationException
{
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $message = sprintf('Property with name [%s] does not exist', $name);
        parent::__construct($message);
    }
}
