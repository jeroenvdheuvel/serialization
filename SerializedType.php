<?php
namespace jvdh\Serialization;

class SerializedType
{
    const TYPE_BOOLEAN = 'b';
    const TYPE_NULL = 'N';
    const TYPE_INTEGER = 'i';
    const TYPE_STRING = 's';
    const TYPE_DOUBLE = 'd';
    const TYPE_ARRAY = 'a';
    const TYPE_OBJECT = 'O';
    const TYPE_REFERENCE_VARIABLE = 'R';
    const TYPE_POINTING_TO_SAME_OBJECT = 'r';
}