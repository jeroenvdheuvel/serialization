README
======

Description
-----------
This library provides the ability to unserialize and re-serialize php serialized data. Unlike the php unserializer, this
library is not creating the actual objects that were serialized. Therefore no error will be thrown when the serialized
class could not be found. This can come in handy when using multiple applications share data via php serialization.


[![Build Status](https://travis-ci.org/jeroenvdheuvel/serialization.svg)](https://travis-ci.org/jeroenvdheuvel/serialization)


Known issues
------------
Although serialized references can be unserialized. Unserialized references are merely a duplicate of the original data.
When serializing this data again, it will differ from the php native serializer. Due to this malfunction, the
unserializer doesn't support references that can cause a loop.

For instance:
```php
$array = [];
$array[] = &$array;
```
