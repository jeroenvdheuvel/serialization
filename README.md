README
======

Description
-----------
This library provides the ability to unserialize and re-serialize php serialized data. Unlike the php unserializer, this
library is not creating the actual objects that are serialized. Therefore no error will be thrown when the serialized
class isn't found. This can come in handy when using multiple applications that share data via php serialization.


Master: [![Build Status](https://secure.travis-ci.org/jeroenvdheuvel/serialization.png?branch=master)](https://travis-ci.org/jeroenvdheuvel/serialization)


Facts!!1one
-----
It's impossible to create a new property on an object
Each serialized property of an object has a value. When the value isn't set explicitly before, the default value will be NULL

It's possible to create a new property on an array

It's possible to change the type of value. For instance a variable containing a boolean can be unserialized, but the same variable can be serialized containing an integer or any other type