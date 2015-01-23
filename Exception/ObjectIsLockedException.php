<?php
namespace jvdh\Serialization\Exception;

class ObjectIsLockedException extends SerializationException
{
    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $message = sprintf('Object with class name [%s] is locked. It\'s not possible to add more properties.' , $className);
        parent::__construct($message);
    }
}
