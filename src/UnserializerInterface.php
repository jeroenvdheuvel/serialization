<?php
namespace jvdh\Serialization;

use Exception;

interface UnserializerInterface
{
    /**
     * @return mixed
     * @throws Exception
     */
    public function unserialize($data);
}