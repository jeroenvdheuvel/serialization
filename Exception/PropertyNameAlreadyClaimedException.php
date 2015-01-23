<?php
namespace jvdh\Serialization\Exception;

class PropertyNameAlreadyClaimedException extends SerializationException
{
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $message = sprintf('Property with name [%s] is already claimed', $name);
        parent::__construct($message);
    }
} 