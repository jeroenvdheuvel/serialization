README
======

Description
-----------
This library provides the ability to unserialize and re-serialize php serialized data. Unlike the php unserializer, this
library is not creating the actual objects that were serialized. Therefore no error will be thrown when the serialized
class could not be found. This can come in handy when using multiple applications share data via php serialization.


Master: [![Build Status](https://travis-ci.org/jeroenvdheuvel/serialization.svg?branch=master)](https://travis-ci.org/jeroenvdheuvel/serialization)

Known issues
------------
HHVM is not able to serialize references containing objects. References containing objects, will be serialized as a
copy/reference pointing to the same object but not to the same variable.


For instance:
```php
$o = new stdClass();
echo serialize(array(&$o, &$o))
```
Should echo `a:2:{i:0;O:8:"stdClass":0:{}i:1;R:2;}` when references are properly supported.
HHVM will echo `a:2:{i:0;O:8:"stdClass":0:{}i:1;r:2;}`. Lowercase `r` means it's not a reference to the same variable.
It's only pointing to the same object.


HHVM does support variables containing references.
For instance:
```php
$i = 1;
echo serialize(array(&$i, &$i));
```
Will echo `a:2:{i:0;i:1;i:1;R:2;}` in both PHP and HHVM.
