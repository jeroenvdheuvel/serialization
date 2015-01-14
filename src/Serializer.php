<?php
namespace jvdh\Serialization;

class Serializer
{
    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        if ($data === null) {
            return SerializedType::TYPE_NULL. ';';
        } elseif (is_bool($data)) {
            return sprintf('%s:%d;', SerializedType::TYPE_BOOLEAN, intval($data));
        } elseif (is_int($data)) {
            return sprintf('%s:%d;', SerializedType::TYPE_INTEGER, $data);
        } elseif (is_float($data)) {
            // TODO: Make this cooler by not using the serialize mechanics.
            return serialize($data);
//            return sprintf('%s:%s;', SerializedType::TYPE_DOUBLE, number_format($data, 16, '.', ''));
        } elseif (is_string($data)) {
            return sprintf('%s:%d:"%s";', SerializedType::TYPE_STRING, strlen($data), $data);
        } elseif (is_array($data)) {
            $arrayDataAsString = '';
            foreach ($data as $key => $value) {
                $arrayDataAsString .= $this->serialize($key);
                $arrayDataAsString .= $this->serialize($value);
            }

            return sprintf('%s:%d:{%s}', SerializedType::TYPE_ARRAY, count($data), $arrayDataAsString);
        }

        // TODO: Handle unsupported type, when there is any
    }
}
