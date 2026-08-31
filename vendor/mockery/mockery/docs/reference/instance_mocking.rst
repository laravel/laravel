.. index::
    single: Mocking; Instance

Instance Mocking
================

Instance mocking means that a statement like:

.. code-block:: php

    $obj = new \MyNamespace\Foo;

...will actually generate a mock object. This is done by replacing the real
class with an instance mock (similar to an alias mock), as with mocking public
methods. The alias will import its expectations from the original mock of
that type (note that the original is never verified and should be ignored
after its expectations are setup). This lets you intercept instantiation where
you can't simply inject a replacement object.

As before, this does not prevent a require statement from including the real
class and triggering a fatal PHP error. It's intended for use where
autoloading is the primary class loading mechanism.
