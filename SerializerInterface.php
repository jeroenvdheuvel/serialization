<?php
namespace jvdh\Serialization;

interface SerializerInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data);
}