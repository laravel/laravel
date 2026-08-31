.. index::
    single: Mocking; Magic Methods

PHP Magic Methods
=================

PHP magic methods which are prefixed with a double underscore, e.g.
``__set()``, pose a particular problem in mocking and unit testing in general.
It is strongly recommended that unit tests and mock objects do not directly
refer to magic methods. Instead, refer only to the virtual methods and
properties these magic methods simulate.

Following this piece of advice will ensure we are testing the real API of
classes and also ensures there is no conflict should Mockery override these
magic methods, which it will inevitably do in order to support its role in
intercepting method calls and properties.
