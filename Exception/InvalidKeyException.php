<?php
namespace jvdh\Serialization\Exception;

class InvalidKeyException extends SerializationException
{
    public function __construct()
    {
        parent::__construct('Trying to parse an invalid key');
    }
}
